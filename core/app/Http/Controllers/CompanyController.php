<?php

namespace App\Http\Controllers;

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordReader;
use App\Models\CompanyKnowledge;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\LocalNumberRequestMail;
use App\Mail\LocalNumberRequestAdminMail;

class CompanyController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company; // assuming one company per user
        return view('companies.edit', compact('company'));
    }

    public function create() {
        return view('companies.create');
    }

    public function store(Request $request) {

        if (auth()->user()->company) {
            return redirect()->route('company.edit')->with('info', 'You already have a company. You can edit it here.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'ai_name' => 'nullable|string|max:100',
            'africastalking_number' => 'nullable|string|max:20',
            'vapi_number' => 'nullable|string|max:255',
            'assistant_description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $data['user_id'] = auth()->id();
        \App\Models\Company::create($data);

        return redirect()->route('company.create')->with('success', 'Company profile created successfully!');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        ]);

        $company = auth()->user()->company;

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($data);

        return back()->with('success', 'Company information updated successfully!');
    }

    public function editVirtualNumbers()
    {
        $company = auth()->user()->company;

        return view('companies.virtual-numbers', compact('company'));
    }



    public function requestNumber(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;
        $type = strtolower(trim($request->input('type'))); 

        $prices = [
            'local' => 6500,
            'international' => 3000,
        ];

        if (!isset($prices[$type])) {
            return back()->with('error', 'Invalid number type selected.');
        }

        $price = $prices[$type];
        $vat = $price * 0.075; // 7.5% VAT
        $total = $price + $vat;

        // Check wallet balance
        if ($user->balance < $total) {
            return back()->with('error', 'Insufficient wallet balance. Please fund your wallet.');
        }

        try {
            // Deduct using bavix/laravel-wallet
            $user->withdraw($total, [
                'description' => "Purchase of {$type} number (₦{$price}) + VAT (₦" . number_format($vat, 2) . ")",
            ]);

            if ($type === 'local') {

                // Send email notifying that request is being processed

                 Mail::send('emails.local_number_request', [
                        'user_name' => $user->name,
                        'email' => $user->email,
                        'company_name' => $company->name,
                        'type' => $type,
                        'price' => $price,
                        'vat' => $vat,
                        'total' => $total,
                    ], function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('Your Local Number Request is Being Processed');
                    });


                  Mail::send('emails.local_number_request_admin', [
                    'user_name' => $user->name,
                    'email' => $user->email,
                    'company_name' => $company->name,
                    'type' => $type,
                    'price' => $price,
                    'vat' => $vat,
                    'total' => $total,
                ], function ($message) {
                    $message->to('request@naitalk.com')
                            ->subject('New Local Number Request Submitted');
                });

                return back()->with('info', 'Your request for a local number is being processed. You will receive an update soon.');
            }

              // 🌍 INTERNATIONAL number — create in real time via Vapi
                $vapiUrl = 'https://api.vapi.ai/phone-number/buy';
                $vapiApiKey = config('services.vapi.api_key');
                $credentialId = config('services.vapi.credential_id'); // from your Vapi dashboard
                $assistantId =  $company->assistant_id;
                $name = $company->name;
                $serverUrl = route('vapi.callback');

                $response = Http::withToken($vapiApiKey)->post($vapiUrl, [ 
                    'areaCode' => '234',
                    'name' => $name,
                    'assistantId' => $assistantId,
                    'serverUrl' =>  'https://voice.naitalk.com/api/vapi/callback',
                ]);

                $data = $response->json();
                \Log::info($data);
                $assignedNumber = $data['number'];

                // Save to database
                $company->update(['vapi_number' => $assignedNumber]);

                // Notify user
                Mail::send('emails.international_number_assigned', [
                    'user_name' => $user->name,
                    'number' => $assignedNumber,
                ], function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Your International Number Has Been Assigned');
                });

                return back()->with('success', "International number assigned successfully: {$assignedNumber}");

        } catch (\Exception $e) {
            return back()->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }



    public function editAIAssistant()
    {
        $company = auth()->user()->company;

        return view('companies.ai-assistant', compact('company'));
    }


    public function updateAIAssistant(Request $request)
    {
        $data = $request->validate([
            'ai_name' => 'required|string|max:30',
            'assistant_description' => 'required|string',
            'welcome_message' => 'required|string|max:50',
        ]);
        $company = auth()->user()->company;

      
        try {
            $vapiToken = config('services.vapi.api_key'); // put in config/services.php
            $vapiUrl = 'https://api.vapi.ai/assistant';
            $response = Http::withToken($vapiToken)
                ->post($vapiUrl, [
                    'name' => $data['ai_name']."-".$company->name,
                    'voice' => [
                        'provider' => '11labs',
                        'voiceId' => 'EXAVITQu4vr4xnSDxMaL',
                    ],
                    'firstMessage' => $data['welcome_message'],
                    'model' => [
                        'provider' => 'openai',
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => $data['assistant_description']
                            ]
                        ]
                    ],
                ]);


            $assistant = $response->json();
            $data['assistant_id'] = $assistant['id'];

            $company->update($data);

            return back()->with('success', 'AI Assistant information saved successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }



    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,txt|max:5120', // 5MB max
        ]);

        $company = auth()->user()->company;

        $file = $request->file('file');
        $filePath = $file->store('company_files', 'public');

        $company->files()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
        ]);

        // === Extract text from file ===
        $extension = strtolower($file->getClientOriginalExtension());
        $text = '';

        if ($extension === 'pdf') {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($file->getPathname());
            $text = $pdf->getText();
        } elseif (in_array($extension, ['doc', 'docx'])) {
            $phpWord = WordReader::load($file->getPathname());
            foreach ($phpWord->getSections() as $section) {
                $elements = $section->getElements();
                foreach ($elements as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . " ";
                    }
                }
            }
        } elseif ($extension === 'txt') {
            $text = file_get_contents($file->getPathname());
        }

        $text = Str::limit(trim(preg_replace('/\s+/', ' ', $text)), 5000, '...'); // Keep it short for now

        // === Store in knowledge base ===
        CompanyKnowledge::create([
            'company_id' => $company->id,
            'company_file_id' => $companyFile->id ?? null,
            'content' => $text,
        ]);


        return back()->with('success', 'File uploaded successfully!');
    }


    public function deleteFile($id)
    {
        $file = \App\Models\CompanyFile::findOrFail($id);

        if ($file->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        \Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'File deleted successfully!');
    }


}

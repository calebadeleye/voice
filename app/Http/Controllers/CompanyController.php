<?php

namespace App\Http\Controllers;

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordReader;
use App\Models\CompanyKnowledge;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

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
            'openai_api_key' => 'nullable|string|max:255',
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
            'ai_name' => 'nullable|string|max:100',
            'africastalking_number' => 'nullable|string|max:20',
            'openai_api_key' => 'nullable|string|max:255',
            'assistant_description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        ]);

        $company = auth()->user()->company;

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($data);

        return back()->with('success', 'Company information updated successfully!');
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

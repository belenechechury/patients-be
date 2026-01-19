<?php

namespace App\Services;

use App\Jobs\SendPatientConfirmationJob;
use App\Models\Patient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PatientService
{
    public function list(
        int $page = 1,
        int $page_size = 10,
        string $search = '',
        string $sort_by = 'first_name',
        ?string $created_from = null,
        ?string $created_to = null
    ) {
        $query = Patient::query();

        // Apply search
        if (!empty($search)) {
            $search_term = strtolower($search);
            $query->where(function ($q) use ($search_term) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$search_term}%"])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search_term}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search_term}%"]);
            });
        }

        // Apply created_at filter if provided
        if ($created_from) {
            $query->whereDate('created_at', '>=', $created_from);
        }
        if ($created_to) {
            $query->whereDate('created_at', '<=', $created_to);
        }

        // Allowed sort columns
        $allowed_sorts = ['first_name', 'last_name', 'email', 'created_at'];
        if (!empty($sort_by) && in_array($sort_by, $allowed_sorts)) {
            $query->orderBy($sort_by);
        }

        return $query->paginate(
            perPage: $page_size,
            columns: ['*'],
            pageName: 'page',
            page: $page
        );
    }

    public function create(array $data, UploadedFile $documentImage): Patient
    {
        $path = $this->storeDocumentImage($documentImage);

        $patient = Patient::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'country_iso' => $data['country_iso'],
            'phone_number' => $data['phone_number'],
            'document_image_path' => $path,
        ]);

        SendPatientConfirmationJob::dispatch($patient);

        return $patient;
    }

    public function update(Patient $patient, array $data, ?UploadedFile $documentImage = null): Patient
    {
        if ($documentImage) {
            $patient->document_image_path = $this->storeDocumentImage($documentImage);
        }

        $patient->update($data);

        return $patient;
    }

    public function delete(Patient $patient): void
    {
        $patient->delete();
    }

    private function storeDocumentImage(UploadedFile $image): string
    {
        return $image->storeAs(
            'documents',
            Str::uuid() . '.jpg',
            'public'
        );
    }
}

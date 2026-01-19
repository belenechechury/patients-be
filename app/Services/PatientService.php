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
        int $pageSize = 10,
        string $search = '',
        string $sortBy = 'first_name'
    ) {
        $query = Patient::query();

        if (!empty($search)) {
            $searchTerm = strtolower($search); // lowercase input
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$searchTerm}%"])
                ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$searchTerm}%"])
                ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchTerm}%"]);
            });
        }

        $allowedSorts = ['first_name', 'last_name', 'email', 'created_at'];
        if (!empty($sortBy) && in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy);
        }

        return $query->paginate(
            perPage: $pageSize,
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

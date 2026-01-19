<?php

namespace App\Services;

use App\Jobs\SendPatientConfirmationJob;
use App\Models\Patient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PatientService
{
    public function list(int $perPage = 10)
    {
        return Patient::latest()->paginate($perPage);
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

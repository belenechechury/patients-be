<?php

namespace App\Services;

use App\Exceptions\PatientException;
use App\Jobs\SendPatientConfirmationJob;
use App\Models\Patient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class PatientService
{
    public function list(int $perPage = 10)
    {
        return Patient::latest()->paginate($perPage);
    }

    public function create(array $data, UploadedFile $documentImage): Patient
    {
        try {
            return DB::transaction(function () use ($data, $documentImage) {
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
            });
        } catch (Throwable $e) {
            Log::error('Error creating patient', [
                'exception' => $e,
            ]);

            throw new PatientException(
                'No se pudo registrar el paciente en este momento'
            );
        }
    }

    public function update(
        Patient $patient,
        array $data,
        ?UploadedFile $documentImage = null
    ): Patient {
        try {
            if ($documentImage) {
                $data['document_image_path'] = $this->storeDocumentImage($documentImage);
            }

            $patient->update($data);

            return $patient;
        } catch (Throwable $e) {
            Log::error('Error updating patient', [
                'patient_id' => $patient->id,
                'exception' => $e,
            ]);

            throw new PatientException(
                'No se pudo actualizar el paciente'
            );
        }
    }

    public function delete(Patient $patient): void
    {
        try {
            $patient->delete();
        } catch (Throwable $e) {
            Log::error('Error deleting patient', [
                'patient_id' => $patient->id,
                'exception' => $e,
            ]);

            throw new PatientException(
                'No se pudo eliminar el paciente'
            );
        }
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Services\PatientService;

class PatientController extends Controller
{
    public function __construct(
        private readonly PatientService $service
    ) {}

    public function index()
    {
        return $this->service->list();
    }

    public function store(StorePatientRequest $request)
    {
        $patient = $this->service->create(
            $request->validated(),
            $request->file('document_image')
        );

        return response()->json([
            'message' => 'Paciente registrado correctamente',
            'data' => $patient,
        ], 201);
    }

    public function show(Patient $patient)
    {
        return $patient;
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $patient = $this->service->update(
            $patient,
            $request->validated(),
            $request->file('document_image')
        );

        return response()->json($patient);
    }

    public function destroy(Patient $patient)
    {
        $this->service->delete($patient);

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListPatientRequest;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Services\PatientService;
use App\Http\Resources\PatientResource;

class PatientController extends Controller
{
    public function __construct(
        private readonly PatientService $service
    ) {}

    public function index(ListPatientRequest $request)
    {
        $data = $request->validatedData();

        $patients = $this->service->list(
            $data['page'],
            $data['page_size'],
            $data['search'],
            $data['sort_by'],
            $data['created_from'],
            $data['created_to']
        );

        return PatientResource::collection($patients);
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

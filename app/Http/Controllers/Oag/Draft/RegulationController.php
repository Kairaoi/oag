<?php

namespace App\Http\Controllers\Oag\Draft;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Draft\RegulationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RegulationController extends Controller
{
    protected $regulationRepository;

    public function __construct(RegulationRepository $regulationRepository)
    {
        $this->regulationRepository = $regulationRepository;
    }

    public function index()
    {
        return view('oag.legal.regulations.index');
    }

    public function create()
    {
        return view('oag.legal.regulations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:regulations',
            'receipt_date' => 'required|date',
            'ministry_id' => 'required|exists:ministries,id',
            'status' => 'required|in:Pending,In Review,Approved,Published,Rejected',
            'priority' => 'required|in:Normal,Urgent,High Priority',
        ]);

        $data = $validated;
        $this->regulationRepository->create($data);

        return redirect()->route('regulations.index')->with('success', 'Regulation created successfully.');
    }

    public function edit($id)
    {
        $regulation = $this->regulationRepository->getById($id);

        if (!$regulation) {
            return redirect()->route('regulations.index')->with('error', 'Regulation not found.');
        }

        return view('oag.legal.regulations.edit', compact('regulation'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:regulations,name,' . $id,
            'receipt_date' => 'required|date',
            'ministry_id' => 'required|exists:ministries,id',
            'status' => 'required|in:Pending,In Review,Approved,Published,Rejected',
            'priority' => 'required|in:Normal,Urgent,High Priority',
        ]);

        $data = $validated;
        $this->regulationRepository->update($id, $data);

        return redirect()->route('regulations.index')->with('success', 'Regulation updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = $this->regulationRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Regulation not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Regulation deleted successfully']);
    }
}

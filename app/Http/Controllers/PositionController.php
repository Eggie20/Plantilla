<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Helpers\ActivityLogger;

class PositionController extends Controller
{
    /**
     * Display a listing of all positions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $positions = Position::with('office')->get();

            return response()->json([
                'success' => true,
                'positions' => $positions
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching positions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load positions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of vacant positions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVacantPositions(Request $request)
    {
        try {
            // Get filter value from request
            $filter = $request->query('filter', 'all');

            $query = Position::with('office');

            // Apply filter based on the selected option
            switch ($filter) {
                case 'vacant':
                    $query->where('status', 'Vacant');
                    break;
                case 'unfunded':
                    $query->where('status', 'Unfunded');
                    break;
                case 'all':
                default:
                    $query->where(function($q) {
                        $q->where('status', 'Vacant')
                          ->orWhere('status', 'Unfunded')
                          ->orWhereNull('status');
                    });
                    break;
            }

            $positions = $query->get()
                ->map(function($position) {
                    return [
                        'id' => $position->id,
                        'itemNo' => $position->itemNo,
                        'position' => $position->position,
                        'salaryGrade' => $position->salaryGrade,
                        'monthlySalary' => $position->monthlySalary,
                        'step' => $position->step,
                        'status' => $position->status,
                        'code' => $position->code,
                        'level' => $position->level,
                        'office' => $position->office ? [
                            'id' => $position->office->id,
                            'name' => $position->office->name,
                            'code' => $position->office->code,
                            'abbreviation' => $position->office->abbreviation
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'positions' => $positions
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching vacant positions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load vacant positions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAssignedPositions()
    {
        try {
            // Get positions with status 'Filled'
            $positions = Position::with(['office', 'personnel'])
                ->where('status', 'Filled')
                ->get()
                ->map(function($position) {
                    try {
                        return [
                            'id' => $position->id,
                            'itemNo' => $position->itemNo,
                            'position' => $position->position,
                            'salaryGrade' => $position->salaryGrade,
                            'monthlySalary' => $position->monthlySalary,
                            'step' => $position->step,
                            'status' => $position->status,
                            'office' => $position->office ? [
                                'id' => $position->office->id,
                                'name' => $position->office->name,
                                'code' => $position->office->code,
                                'abbreviation' => $position->office->abbreviation
                            ] : null,
                            'personnel' => $position->personnel ? [
                                'id' => $position->personnel->id,
                                'firstName' => $position->personnel->firstName,
                                'lastName' => $position->personnel->lastName
                            ] : null
                        ];
                    } catch (\Exception $e) {
                        Log::error('Error processing position ' . $position->id . ': ' . $e->getMessage());
                        throw $e;
                    }
                });

            Log::info('Found ' . count($positions) . ' assigned positions');

            return response()->json([
                'success' => true,
                'positions' => $positions
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching assigned positions: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load assigned positions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created position in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate request
            $validated = $request->validate([
                'itemNo' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:positions,itemNo',
                    'regex:/^[A-Z0-9\-\s]+$/i'
                ],
                'office_id' => 'required|exists:offices,id',
                'position' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[A-Z0-9\s\.,\-\(\)]+$/i'
                ],
                'salaryGrade' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^[0-9]{1,3}$/',
                    function($attribute, $value, $fail) {
                        $grade = intval($value);
                        if ($grade < 1 || $grade > 34) {
                            $fail('Salary Grade must be between 1 and 34');
                        }
                    }
                ],
                'step' => [
                    'nullable',
                    'integer',
                    'min:1',
                    'max:8',
                    function($attribute, $value, $fail) {
                        if ($value !== null && !in_array($value, range(1, 8))) {
                            $fail('Step must be between 1 and 8');
                        }
                    }
                ],
                'code' => [
                    'nullable',
                    'string',
                    'max:50',
                    'regex:/^[A-Z0-9\-]+$/i'
                ],
                'type' => [
                    'nullable',
                    'string',
                    'max:1',
                    Rule::in(['M', 'P'])
                ],
                'level' => [
                    'nullable',
                    'string',
                    'max:1',
                    Rule::in(['K', 'A'])
                ],
                'status' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::in(['Vacant', 'Filled', 'Unfunded'])
                ]
            ], [
                'position.required' => 'Position Title is required',
                'position.max' => 'Position Title must be less than 255 characters',
                'salaryGrade.required' => 'Salary Grade is required',
                'salaryGrade.regex' => 'Salary Grade must be a valid number',
                'step.min' => 'Step must be at least 1',
                'step.max' => 'Step must be at most 8'
            ]);

            // Create position
            $position = Position::create([
                'itemNo' => $validated['itemNo'],
                'office_id' => $validated['office_id'],
                'position' => $validated['position'],
                'salaryGrade' => $validated['salaryGrade'],
                'step' => $validated['step'] ?? 1,
                'code' => $validated['code'],
                'type' => $validated['type'] ?? 'M',
                'level' => $validated['level'],
                'status' => $validated['status'] ?? 'Vacant'
            ]);

            ActivityLogger::log('positions', 'Position was created', $position);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Position created successfully',
                'position' => $position
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating position: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create position: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified position.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Get position with office relationship
            $position = Position::with('office')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'position' => $position
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving position: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve position: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified position.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            $position = Position::with('office')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'position' => $position
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching position for edit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load position: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified position in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $position = Position::findOrFail($id);

            // Validate request
            $validated = $request->validate([
                'itemNo' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('positions')->ignore($position->id)
                ],
                'office_id' => 'required|exists:offices,id',
                'position' => 'required|string|max:255',
                'salaryGrade' => 'required|string|max:20',
                'step' => 'nullable|integer|min:1|max:8',
                'code' => 'nullable|string|max:50',
                'type' => 'nullable|string|max:1',
                'level' => 'nullable|string|max:1',
                'status' => 'nullable|string|max:50'
            ]);

            // Update position
            $position->update([
                'itemNo' => $validated['itemNo'],
                'office_id' => $validated['office_id'],
                'position' => $validated['position'],
                'salaryGrade' => $validated['salaryGrade'],
                'step' => $validated['step'] ?? $position->step,
                'code' => $validated['code'],
                'type' => $validated['type'] ?? $position->type,
                'level' => $validated['level'],
                'status' => $validated['status'] ?? $position->status
            ]);

            // Log the activity
            ActivityLogger::log('positions', 'Position was updated', $position, [
                'changes' => $validated,
                'office' => $position->office,
                'previous_status' => $position->getOriginal('status')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Position updated successfully',
                'position' => $position
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating position:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update position'
            ], 500);
        }
    }

    /**
     * Remove the specified position from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $position = Position::findOrFail($id);
            
            // Check if the position has any assigned personnel
            if ($position->personnel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete position. It has assigned personnel.'
                ], 400);
            }

            $position->delete();

            return response()->json([
                'success' => true,
                'message' => 'Position deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting position: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting position: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkItemNo(Request $request)
    {
        try {
            $itemNo = $request->input('itemNo');
            $id = $request->input('id');

            if (empty($itemNo)) {
                return response()->json([
                    'exists' => true,
                    'message' => 'Item number is required'
                ], 422);
            }

            $query = Position::where('itemNo', $itemNo);
            
            if (!empty($id)) {
                $query->where('id', '!=', $id);
            }

            $exists = $query->exists();

            return response()->json([
                'exists' => $exists
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking item number: ' . $e->getMessage());
            return response()->json([
                'exists' => true,
                'message' => 'Error checking item number'
            ], 500);
        }
    }
}
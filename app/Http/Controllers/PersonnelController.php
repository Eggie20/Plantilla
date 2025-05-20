<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\Position;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\Models\Activity;
use App\Helpers\ActivityLogger;
use App\Helpers\OfficeFormatter; // Import the OfficeFormatter class

class PersonnelController extends Controller
{
    private function cleanSalaryValue($value) {
        // Just remove any commas from the value
        return str_replace(',', '', $value);
    }

    /**
     * Helper function to log activities
     */
    private function logActivity($description, $model, $properties = [])
    {
        try {
            // Get the model's attributes
            $attributes = $model->getAttributes();
            
            // Use ActivityLogger with 'personnels' as log_name
            ActivityLogger::log(
                'personnels',
                $description,
                $model,
                array_merge($attributes, $properties)
            );

        } catch (\Exception $e) {
            Log::error('Activity log insertion failed: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'description' => $description
            ]);
            throw $e;
        }
    }

    /**
     * Helper method to manage vacant positions
     * 
     * @param string $officeCode
     * @param string $itemNo
     * @param bool $isVacant
     * @return void
     */
    private function manageVacantPosition($officeCode, $itemNo, $isVacant = true)
    {
        try {
            DB::table('vacant_positions')
                ->updateOrInsert(
                    [
                        'office_code' => $officeCode,
                        'item_no' => $itemNo
                    ],
                    [
                        'is_vacant' => $isVacant,
                        'updated_at' => now()
                    ]
                );
            
            // Use ActivityLogger with 'personnels' as log_name
            ActivityLogger::log(
                'personnels',
                $isVacant ? 'Position marked as vacant' : 'Position marked as filled',
                new \stdClass(),
                [
                    'office_code' => $officeCode,
                    'item_no' => $itemNo,
                    'is_vacant' => $isVacant
                ]
            );

        } catch (\Exception $e) {
            Log::error('Vacant position update error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function index()
    {
        // Eager load the office relationship to avoid N+1 queries
        $personnels = Personnel::with('office')
            ->get()
            ->map(function($personnel) {
                // Get the office name from the relationship
                $officeName = '';
                if ($personnel->office) {
                    $officeName = $personnel->office->name;
                } else if ($personnel->office) {
                    // If no relationship but office code exists, try to get the office
                    $office = Office::where('code', $personnel->office)->first();
                    if ($office) {
                        $officeName = $office->name;
                    }
                }
                
                // Format the office name using OfficeFormatter
                $personnel->office = OfficeFormatter::format($officeName);
                return $personnel;
            });
        
        $offices = Office::all();
        
        // Prepare office mapping for JavaScript
        $officeMapping = $offices->mapWithKeys(function($office) {
            return [$office->code => $office->name];
        })->toArray();
        
        return view('Plantilla.index', compact('personnels', 'offices'))
            ->with('officeMapping', json_encode($officeMapping));
    }

    public function getPersonnelByItemNo(Request $request)
    {
        $itemNo = $request->input('itemNo');
        
        $personnel = Personnel::where('itemNo', $itemNo)
            ->with('position')
            ->first();
            
        if ($personnel) {
            return response()->json([
                'success' => true,
                'personnel' => $personnel
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No personnel found with this item number'
        ], 404);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $personnel = Personnel::findOrFail($id);
            
            // Validate all fields including office
            $validated = $request->validate([
                'office' => 'required|exists:offices,code',
                'itemNo' => 'required',
                'position' => 'required',
                'salaryGrade' => 'required|numeric',
                'authorizedSalary' => 'required|numeric',
                'actualSalary' => 'required|numeric',
                'step' => 'required|numeric',
                'code' => 'required',
                'type' => 'required',
                'level' => 'required',
                'lastName' => 'required',
                'firstName' => 'required',
                'middleName' => 'nullable',
                'dob' => 'required|date',
                'originalAppointment' => 'required|date',
                'lastPromotion' => 'nullable|date',
                'status' => 'required'
            ]);

            // Clean up salary values
            $validated['authorizedSalary'] = $this->cleanSalaryValue($validated['authorizedSalary']);
            $validated['actualSalary'] = $this->cleanSalaryValue($validated['actualSalary']);

            // Update the personnel record
            $personnel->update($validated);
            
            // Log the activity
            $this->logActivity('Personnel was updated', $personnel);
            
            // Get the updated personnel data with formatted office
            $personnelData = $personnel->toArray();
            // Use the office code directly since we're storing it directly
            $officeCode = $personnel->office;
            $personnelData['office'] = OfficeFormatter::format($officeCode); // Format the office name
            
            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $personnelData
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Personnel update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // Get the personnel record
            $personnel = Personnel::with(['position', 'office'])->findOrFail($id);
            
            // Log the deletion
            try {
                ActivityLogger::log(
                    'personnels',
                    'Personnel was deleted',
                    $personnel,
                    [
                        'office' => $personnel->office ? $personnel->office->name : $personnel->office,
                        'position' => $personnel->position ? $personnel->position->name : $personnel->position,
                        'fullName' => $personnel->fullName()
                    ]
                );
            } catch (\Exception $logError) {
                Log::warning('Activity log failed but continuing with deletion: ' . $logError->getMessage());
            }

            // Update vacant positions if possible
            try {
                if ($personnel->office && $personnel->itemNo) {
                    $this->manageVacantPosition($personnel->office, $personnel->itemNo, true);
                }
            } catch (\Exception $vacancyError) {
                Log::warning('Vacant position update failed but continuing with deletion: ' . $vacancyError->getMessage());
            }

            // Delete the personnel record
            try {
                $personnel->delete();
            } catch (\Exception $deleteError) {
                DB::rollBack();
                throw new \Exception('Failed to delete personnel record: ' . $deleteError->getMessage());
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Personnel deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Personnel not found: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Personnel not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Personnel delete error: ' . $e->getMessage(), [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Provide a more detailed error message to the client
            return response()->json([
                'success' => false,
                'message' => 'Error deleting personnel: ' . $e->getMessage(),
                'details' => [
                    'error_type' => get_class($e),
                    'error_message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    public function assign(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Clean up salary values - just remove commas
            $request->merge([
                'authorizedSalary' => $this->cleanSalaryValue($request->authorizedSalary),
                'actualSalary' => $this->cleanSalaryValue($request->actualSalary)
            ]);
            
            // Log the assignment request
            Log::info('Personnel assignment request:', $request->all());
            
            // Validate the request data using the model's validation rules
            $validatedData = $request->validate(Personnel::$rules, Personnel::$messages);
            
            // Get the position
            $position = Position::findOrFail($validatedData['position_id']);
            
            // Check if the position is already assigned
            if ($position->status !== 'Vacant' && $position->status !== 'Unfunded') {
                return response()->json([
                    'success' => false,
                    'message' => 'This position is already assigned to someone else.'
                ], 422);
            }
            
            // Create the personnel record with office and position details
            $personnel = Personnel::create([
                'office' => $position->office->code, // Get office code from position
                'itemNo' => $position->itemNo, // Add itemNo from position
                'position' => $position->position, // Add position title
                'salaryGrade' => $position->salaryGrade, // Add salary grade
                'step' => $position->step, // Add step from position
                'code' => $position->code, // Add position code
                'type' => $position->type, // Add position type
                'level' => $position->level, // Add position level
                'lastName' => $validatedData['lastName'],
                'firstName' => $validatedData['firstName'],
                'middleName' => $validatedData['middleName'],
                'dob' => $validatedData['dob'],
                'authorizedSalary' => $validatedData['authorizedSalary'],
                'actualSalary' => $validatedData['actualSalary'],
                'originalAppointment' => $validatedData['originalAppointment'],
                'lastPromotion' => $validatedData['lastPromotion'],
                'status' => $validatedData['status'] // Status comes from form
            ]);
            
            // Update the position status to Filled since it's now assigned
            $position->update([
                'status' => 'Filled', // Position status is always set to Filled when assigned
                'personnel_id' => $personnel->id
            ]);
            
            // Log the activity
            ActivityLogger::log(
                'personnels',
                'Personnel was assigned to position',
                $personnel,
                [
                    'position' => $position->position,
                    'office' => $position->office->name,
                    'itemNo' => $position->itemNo,
                    'salaryGrade' => $position->salaryGrade,
                    'code' => $position->code,
                    'type' => $position->type,
                    'level' => $position->level,
                    'personnel_status' => $personnel->status // Log the personnel status
                ]
            );
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Personnel assigned successfully',
                'personnel' => $personnel
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning personnel:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while assigning personnel: ' . $e->getMessage()
            ], 500);
        }
    }

    public function retire(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validate([
                'personnel_id' => 'required|exists:personnels,id',
                'retirement_date' => 'required|date|after_or_equal:today'
            ]);

            $personnel = Personnel::findOrFail($validated['personnel_id']);
            
            // Update personnel with pending retirement status
            $personnel->pendingRetirement = true;
            $personnel->retirement_date = $validated['retirement_date'];
            $personnel->save();

            // Log the activity
            $this->logActivity('Retirement request submitted', $personnel);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Retirement request has been submitted and is pending approval.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Retirement request error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

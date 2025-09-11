<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payslip;
use App\Models\SalaryChart;
use App\Models\ConnectionLog;
use App\Models\PasswordResetRequest;
use App\Models\Event;
use App\Models\Announcement;
use App\Models\AdministrativeDocument;
use App\Models\EmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get all employees
     */
    public function getEmployees()
    {
        $employees = User::orderBy('created_at', 'desc')->get();
        return response()->json($employees);
    }

    /**
     * Create a new employee
     */
    public function createEmployee(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|min:1',
            'last_name' => 'required|string|min:1',
            'email' => 'nullable|email',
            'login' => 'required|string|min:3|unique:users',
            'password' => 'required|string|min:6',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'hire_date' => 'nullable|date',
        ]);

        // Generate employee ID if not provided
        $employeeId = 'EMP' . time();
        
        // Generate email if not provided
        $email = $request->email ?? (strtolower($employeeId) . '@anpt.dz');

        $user = User::create([
            'email' => $email,
            'role' => 'employee',
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'employee_id' => $employeeId,
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'department' => $request->department ?? '',
            'position' => $request->position ?? '',
            'hire_date' => $request->hire_date ?? now()->toDateString(),
            'is_active' => true,
        ]);

        return response()->json([
            'user' => $user,
            'message' => "Employé créé avec succès. Email: $email, ID: $employeeId, Login: {$request->login}",
            'credentials' => [
                'email' => $email,
                'employeeId' => $employeeId,
                'login' => $request->login,
                'password' => $request->password
            ]
        ]);
    }

    /**
     * Upload payslip
     */
    public function uploadPayslip(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'file' => 'nullable|file|mimes:pdf|max:10240', // Optional for now
        ]);

        // In a real implementation, you would handle file upload here
        $fileName = "payslip_{$request->user_id}_{$request->month}_{$request->year}.pdf";
        $fileUrl = "storage/payslips/$fileName";
        
        // If file is provided, store it
        if ($request->hasFile('file')) {
            $path = $request->file('file')->storeAs('payslips', $fileName, 'public');
            $fileUrl = Storage::url($path);
        } else {
            // Simulate for demo
            $fileUrl = "https://anpt.dz/payslips/$fileName";
        }

        $payslip = Payslip::create([
            'user_id' => $request->user_id,
            'file_name' => $fileName,
            'file_url' => $fileUrl,
            'month' => $request->month,
            'year' => $request->year,
            'uploaded_by' => auth()->user()->email,
        ]);

        return response()->json($payslip);
    }

    /**
     * Upload salary chart
     */
    public function uploadSalaryChart(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:1',
            'file' => 'nullable|file|mimes:pdf|max:10240', // Optional for now
        ]);

        // Deactivate previous charts
        SalaryChart::where('is_active', true)->update(['is_active' => false]);

        // In a real implementation, you would handle file upload here
        $fileName = "salary_chart_" . time() . ".pdf";
        $fileUrl = "storage/charts/$fileName";
        
        // If file is provided, store it
        if ($request->hasFile('file')) {
            $path = $request->file('file')->storeAs('charts', $fileName, 'public');
            $fileUrl = Storage::url($path);
        } else {
            // Simulate for demo
            $fileUrl = "https://anpt.dz/charts/$fileName";
        }

        $salaryChart = SalaryChart::create([
            'title' => $request->title,
            'file_name' => $fileName,
            'file_url' => $fileUrl,
            'uploaded_by' => auth()->user()->email,
            'is_active' => true,
        ]);

        return response()->json($salaryChart);
    }

    /**
     * Get connection logs
     */
    public function getConnectionLogs()
    {
        $logs = ConnectionLog::orderBy('login_at', 'desc')->limit(100)->get();
        return response()->json($logs);
    }

    /**
     * Get password reset requests
     */
    public function getPasswordRequests()
    {
        $requests = PasswordResetRequest::where('status', 'pending')
            ->orderBy('requested_at', 'desc')
            ->get();
        return response()->json($requests);
    }

    /**
     * Get all events
     */
    public function getEvents()
    {
        $events = Event::orderBy('event_date', 'desc')->get();
        return response()->json($events);
    }

    /**
     * Create event
     */
    public function createEvent(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'location' => 'nullable|string',
        ]);

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'created_by' => auth()->user()->email,
            'is_active' => true,
        ]);

        return response()->json($event);
    }

    /**
     * Update event
     */
    public function updateEvent(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'location' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $event = Event::findOrFail($id);
        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'is_active' => $request->is_active,
        ]);

        return response()->json($event);
    }

    /**
     * Delete event
     */
    public function deleteEvent($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Get all announcements
     */
    public function getAnnouncements()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->get();
        return response()->json($announcements);
    }

    /**
     * Create announcement
     */
    public function createAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'priority' => 'nullable|string|in:normal,medium,high',
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'priority' => $request->priority ?? 'normal',
            'created_by' => auth()->user()->email,
            'is_active' => true,
        ]);

        return response()->json($announcement);
    }

    /**
     * Update announcement
     */
    public function updateAnnouncement(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'priority' => 'nullable|string|in:normal,medium,high',
            'is_active' => 'boolean',
        ]);

        $announcement = Announcement::findOrFail($id);
        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'priority' => $request->priority,
            'is_active' => $request->is_active,
        ]);

        return response()->json($announcement);
    }

    /**
     * Delete announcement
     */
    public function deleteAnnouncement($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Get all administrative documents
     */
    public function getDocuments()
    {
        $documents = AdministrativeDocument::orderBy('created_at', 'desc')->get();
        return response()->json($documents);
    }

    /**
     * Create administrative document
     */
    public function createDocument(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'document_type' => 'required|string',
            'file' => 'nullable|file|mimes:pdf|max:10240', // Optional for now
        ]);

        // In a real implementation, you would handle file upload here
        $fileName = "admin_doc_" . time() . ".pdf";
        $fileUrl = "storage/documents/$fileName";
        
        // If file is provided, store it
        if ($request->hasFile('file')) {
            $path = $request->file('file')->storeAs('documents', $fileName, 'public');
            $fileUrl = Storage::url($path);
        } else {
            // Simulate for demo
            $fileUrl = "https://anpt.dz/documents/$fileName";
        }

        $document = AdministrativeDocument::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_name' => $fileName,
            'file_url' => $fileUrl,
            'document_type' => $request->document_type,
            'created_by' => auth()->user()->email,
            'is_active' => true,
        ]);

        return response()->json($document);
    }

    /**
     * Update administrative document
     */
    public function updateDocument(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'document_type' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $document = AdministrativeDocument::findOrFail($id);
        $document->update([
            'title' => $request->title,
            'description' => $request->description,
            'document_type' => $request->document_type,
            'is_active' => $request->is_active,
        ]);

        return response()->json($document);
    }

    /**
     * Delete administrative document
     */
    public function deleteDocument($id)
    {
        $document = AdministrativeDocument::findOrFail($id);
        $document->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Get all employee requests
     */
    public function getEmployeeRequests()
    {
        $requests = DB::table('employee_requests')
            ->join('users', 'employee_requests.user_id', '=', 'users.id')
            ->select('employee_requests.*', 'users.first_name', 'users.last_name', 'users.employee_id')
            ->orderBy('employee_requests.created_at', 'desc')
            ->get();

        return response()->json($requests);
    }

    /**
     * Handle employee request
     */
    public function handleEmployeeRequest(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:approved,rejected,completed',
            'admin_response' => 'required|string',
        ]);

        $employeeRequest = EmployeeRequest::findOrFail($id);
        $employeeRequest->update([
            'status' => $request->status,
            'admin_response' => $request->admin_response,
            'handled_by' => auth()->user()->email,
            'handled_at' => now(),
        ]);

        return response()->json($employeeRequest);
    }
}
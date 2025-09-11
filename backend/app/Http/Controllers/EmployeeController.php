<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payslip;
use App\Models\SalaryChart;
use App\Models\Event;
use App\Models\Announcement;
use App\Models\AdministrativeDocument;
use App\Models\EmployeeRequest;

class EmployeeController extends Controller
{
    /**
     * Get authenticated user profile
     */
    public function getProfile()
    {
        return response()->json(Auth::user());
    }
    
    /**
     * Get payslips for authenticated user
     */
    public function getPayslips()
    {
        $payslips = Payslip::where('user_id', Auth::id())
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        return response()->json($payslips);
    }
    
    /**
     * Get the active salary chart
     */
    public function getSalaryChart()
    {
        $salaryChart = SalaryChart::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->first();
        
        return response()->json($salaryChart ?: null);
    }
    
    /**
     * Get active events
     */
    public function getEvents()
    {
        $events = Event::where('is_active', true)
            ->orderBy('event_date', 'asc')
            ->get();
        
        return response()->json($events);
    }
    
    /**
     * Get active announcements
     */
    public function getAnnouncements()
    {
        $announcements = Announcement::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($announcements);
    }
    
    /**
     * Get active administrative documents
     */
    public function getDocuments()
    {
        $documents = AdministrativeDocument::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($documents);
    }
    
    /**
     * Get requests for authenticated user
     */
    public function getRequests()
    {
        $requests = EmployeeRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($requests);
    }
    
    /**
     * Create a new request
     */
    public function createRequest(Request $request)
    {
        $request->validate([
            'request_type' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'request_data' => 'nullable|array',
        ]);
        
        $employeeRequest = EmployeeRequest::create([
            'user_id' => Auth::id(),
            'request_type' => $request->request_type,
            'title' => $request->title,
            'description' => $request->description,
            'request_data' => $request->request_data,
        ]);
        
        return response()->json($employeeRequest);
    }
}
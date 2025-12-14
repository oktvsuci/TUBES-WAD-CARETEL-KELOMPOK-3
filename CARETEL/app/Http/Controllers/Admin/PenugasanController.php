<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penugasan;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PenugasanController extends Controller
{
    /**
     * Display a listing of assignments (READ)
     */
    public function index(Request $request)
    {
        $query = Penugasan::with(['laporan', 'teknisi', 'admin']);
        
        // Filter by teknisi
        if ($request->has('teknisi_id') && $request->teknisi_id != 'all') {
            $query->where('teknisi_id', $request->teknisi_id);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by deadline
        if ($request->has('deadline_filter')) {
            switch ($request->deadline_filter) {
                case 'overdue':
                    $query->where('deadline', '<', now())
                          ->whereNotIn('status', ['selesai', 'dibatalkan']);
                    break;
                case 'today':
                    $query->whereDate('deadline', today());
                    break;
                case 'week':
                    $query->whereBetween('deadline', [now(), now()->addWeek()]);
                    break;
            }
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('laporan', function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $penugasan = $query->paginate(10);
        $teknisiList = User::where('role', 'teknisi')->where('is_active', true)->get();
        
        $stats = [
            'total' => Penugasan::count(),
            'pending' => Penugasan::where('status', 'pending')->count(),
            'diproses' => Penugasan::where('status', 'diproses')->count(),
            'selesai' => Penugasan::where('status', 'selesai')->count(),
            'overdue' => Penugasan::where('deadline', '<', now())
                                   ->whereNotIn('status', ['selesai', 'dibatalkan'])
                                   ->count(),
        ];
        
        return view('admin.penugasan.index', compact('penugasan', 'teknisiList', 'stats'));
    }

    /**
     * Show the form for creating a new assignment (CREATE - Form)
     */
    public function create(Request $request)
    {
        // Get unassigned reports or specific report
        $laporanQuery = Laporan::where('status', 'pending')
                               ->whereDoesntHave('penugasan');
        
        if ($request->has('laporan_id')) {
            $laporanQuery->orWhere('id', $request->laporan_id);
        }
        
        $laporanList = $laporanQuery->get();
        $teknisiList = User::where('role', 'teknisi')->where('is_active', true)->get();
        
        // Calculate teknisi workload
        $teknisiWorkload = [];
        foreach ($teknisiList as $teknisi) {
            $teknisiWorkload[$teknisi->id] = Penugasan::where('teknisi_id', $teknisi->id)
                ->whereIn('status', ['pending', 'diproses'])
                ->count();
        }
        
        return view('admin.penugasan.create', compact('laporanList', 'teknisiList', 'teknisiWorkload'));
    }

    /**
     * Store a newly created assignment (CREATE - Store)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'laporan_id' => 'required|exists:laporan,id',
            'teknisi_id' => 'required|exists:users,id',
            'deadline' => 'required|date|after:now',
            'prioritas' => 'required|in:rendah,sedang,tinggi,darurat',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if laporan already assigned
        $existingPenugasan = Penugasan::where('laporan_id', $request->laporan_id)
                                      ->whereNotIn('status', ['dibatalkan'])
                                      ->first();
        
        if ($existingPenugasan) {
            return redirect()->back()
                ->with('error', 'Laporan ini sudah ditugaskan ke teknisi.')
                ->withInput();
        }

        // Create assignment
        $penugasan = Penugasan::create([
            'laporan_id' => $request->laporan_id,
            'teknisi_id' => $request->teknisi_id,
            'admin_id' => auth()->id(),
            'deadline' => $request->deadline,
            'prioritas' => $request->prioritas,
            'catatan' => $request->catatan,
            'status' => 'pending',
        ]);

        // Update laporan status
        Laporan::where('id', $request->laporan_id)->update([
            'status' => 'ditugaskan',
            'teknisi_id' => $request->teknisi_id,
        ]);

        // TODO: Send notification to teknisi
        
        return redirect()->route('admin.penugasan.index')
            ->with('success', 'Teknisi berhasil ditugaskan ke laporan.');
    }

    /**
     * Display the specified assignment (READ - Detail)
     */
    public function show($id)
    {
        $penugasan = Penugasan::with([
            'laporan.user', 
            'laporan.kategori',
            'teknisi', 
            'admin',
            'progressHistory'
        ])->findOrFail($id);
        
        return view('admin.penugasan.show', compact('penugasan'));
    }

    /**
     * Show the form for editing the assignment (UPDATE - Form)
     */
    public function edit($id)
    {
        $penugasan = Penugasan::with('laporan')->findOrFail($id);
        $teknisiList = User::where('role', 'teknisi')->where('is_active', true)->get();
        
        // Calculate teknisi workload
        $teknisiWorkload = [];
        foreach ($teknisiList as $teknisi) {
            $teknisiWorkload[$teknisi->id] = Penugasan::where('teknisi_id', $teknisi->id)
                ->whereIn('status', ['pending', 'diproses'])
                ->count();
        }
        
        return view('admin.penugasan.edit', compact('penugasan', 'teknisiList', 'teknisiWorkload'));
    }

    /**
     * Update the specified assignment (UPDATE - Store)
     */
    public function update(Request $request, $id)
    {
        $penugasan = Penugasan::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'teknisi_id' => 'required|exists:users,id',
            'deadline' => 'required|date',
            'prioritas' => 'required|in:rendah,sedang,tinggi,darurat',
            'status' => 'required|in:pending,diproses,selesai,dibatalkan',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldTeknisiId = $penugasan->teknisi_id;
        $newTeknisiId = $request->teknisi_id;

        // Update assignment
        $penugasan->update([
            'teknisi_id' => $request->teknisi_id,
            'deadline' => $request->deadline,
            'prioritas' => $request->prioritas,
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);

        // Update laporan if teknisi changed
        if ($oldTeknisiId != $newTeknisiId) {
            Laporan::where('id', $penugasan->laporan_id)->update([
                'teknisi_id' => $newTeknisiId,
            ]);
            
            // TODO: Send notification to both old and new teknisi
        }

        return redirect()->route('admin.penugasan.index')
            ->with('success', 'Penugasan berhasil diperbarui.');
    }

    /**
     * Cancel assignment (DELETE)
     */
    public function destroy($id)
    {
        $penugasan = Penugasan::findOrFail($id);
        
        // Only allow canceling if not completed
        if ($penugasan->status == 'selesai') {
            return redirect()->back()
                ->with('error', 'Tidak dapat membatalkan penugasan yang sudah selesai.');
        }

        $penugasan->update([
            'status' => 'dibatalkan',
            'catatan' => ($penugasan->catatan ?? '') . "\n[Dibatalkan oleh admin pada " . now()->format('d/m/Y H:i') . "]"
        ]);

        // Reset laporan status
        Laporan::where('id', $penugasan->laporan_id)->update([
            'status' => 'pending',
            'teknisi_id' => null,
        ]);

        // TODO: Send notification to teknisi
        
        return redirect()->route('admin.penugasan.index')
            ->with('success', 'Penugasan berhasil dibatalkan.');
    }

    /**
     * Reassign to different teknisi (UPDATE)
     */
    public function reassign(Request $request, $id)
    {
        $penugasan = Penugasan::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'teknisi_id' => 'required|exists:users,id|different:' . $penugasan->teknisi_id,
            'alasan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldTeknisi = $penugasan->teknisi;
        $newTeknisiId = $request->teknisi_id;

        $penugasan->update([
            'teknisi_id' => $newTeknisiId,
            'catatan' => ($penugasan->catatan ?? '') . "\n[Dipindahkan dari " . $oldTeknisi->name . " ke teknisi baru. Alasan: " . $request->alasan . "]"
        ]);

        // Update laporan
        Laporan::where('id', $penugasan->laporan_id)->update([
            'teknisi_id' => $newTeknisiId,
        ]);

        // TODO: Send notification to both teknisi
        
        return redirect()->back()
            ->with('success', 'Penugasan berhasil dipindahkan ke teknisi baru.');
    }

    /**
     * Extend deadline (UPDATE)
     */
    public function extendDeadline(Request $request, $id)
    {
        $penugasan = Penugasan::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'new_deadline' => 'required|date|after:' . $penugasan->deadline,
            'alasan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldDeadline = Carbon::parse($penugasan->deadline)->format('d/m/Y H:i');
        $newDeadline = Carbon::parse($request->new_deadline)->format('d/m/Y H:i');

        $penugasan->update([
            'deadline' => $request->new_deadline,
            'catatan' => ($penugasan->catatan ?? '') . "\n[Deadline diperpanjang dari {$oldDeadline} ke {$newDeadline}. Alasan: " . $request->alasan . "]"
        ]);

        // TODO: Send notification to teknisi
        
        return redirect()->back()
            ->with('success', 'Deadline berhasil diperpanjang.');
    }

    /**
     * Bulk assign teknisi to multiple reports
     */
    public function bulkAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'laporan_ids' => 'required|array',
            'laporan_ids.*' => 'exists:laporan,id',
            'teknisi_id' => 'required|exists:users,id',
            'deadline' => 'required|date|after:now',
            'prioritas' => 'required|in:rendah,sedang,tinggi,darurat',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $count = 0;
        foreach ($request->laporan_ids as $laporanId) {
            // Check if already assigned
            $exists = Penugasan::where('laporan_id', $laporanId)
                               ->whereNotIn('status', ['dibatalkan'])
                               ->exists();
            
            if (!$exists) {
                Penugasan::create([
                    'laporan_id' => $laporanId,
                    'teknisi_id' => $request->teknisi_id,
                    'admin_id' => auth()->id(),
                    'deadline' => $request->deadline,
                    'prioritas' => $request->prioritas,
                    'status' => 'pending',
                ]);

                Laporan::where('id', $laporanId)->update([
                    'status' => 'ditugaskan',
                    'teknisi_id' => $request->teknisi_id,
                ]);

                $count++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} laporan berhasil ditugaskan"
        ]);
    }

    /**
     * Get teknisi availability
     */
    public function teknisiAvailability()
    {
        $teknisiList = User::where('role', 'teknisi')
                           ->where('is_active', true)
                           ->get();
        
        $availability = [];
        
        foreach ($teknisiList as $teknisi) {
            $activeTasks = Penugasan::where('teknisi_id', $teknisi->id)
                                    ->whereIn('status', ['pending', 'diproses'])
                                    ->count();
            
            $completedToday = Penugasan::where('teknisi_id', $teknisi->id)
                                       ->where('status', 'selesai')
                                       ->whereDate('updated_at', today())
                                       ->count();
            
            $availability[] = [
                'id' => $teknisi->id,
                'name' => $teknisi->name,
                'active_tasks' => $activeTasks,
                'completed_today' => $completedToday,
                'availability_score' => 10 - $activeTasks, // Simple scoring
            ];
        }
        
        // Sort by availability
        usort($availability, function($a, $b) {
            return $b['availability_score'] - $a['availability_score'];
        });
        
        return response()->json($availability);
    }

    /**
     * Export assignments to CSV
     */
    public function export(Request $request)
    {
        $query = Penugasan::with(['laporan', 'teknisi', 'admin']);
        
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        $penugasan = $query->get();
        
        $filename = 'penugasan_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($penugasan) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Laporan', 'Teknisi', 'Admin', 'Deadline', 'Prioritas', 'Status', 'Tanggal Dibuat']);
            
            foreach ($penugasan as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->laporan->judul ?? '-',
                    $item->teknisi->name ?? '-',
                    $item->admin->name ?? '-',
                    Carbon::parse($item->deadline)->format('d/m/Y H:i'),
                    ucfirst($item->prioritas),
                    ucfirst($item->status),
                    $item->created_at->format('d/m/Y H:i')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
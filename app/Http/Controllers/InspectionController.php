<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\{
    Checklist,
    Location,
    Inspection,
    InspectionItem,
    InspectionPhoto,
    ChecklistItem
};

class InspectionController extends Controller
{
    /** ===================== DRAFT LIST ===================== */
    public function drafts()
    {
        $drafts = Inspection::where('status','draft')
            ->with('location')
            ->orderBy('updated_at','desc')
            ->get();

        return view('inspections.drafts', compact('drafts'));
    }

    /** ===================== CREATE (DENGAN FOTO) ===================== */
    public function create()
    {
        $checklist  = Checklist::with(['categories.items'])
                        ->where('is_active', true)->firstOrFail();
        $locations  = Location::orderBy('code')->get();
        $existing   = collect();
        $inspection = null;

        return view('inspections.create', compact('checklist','locations','existing','inspection'));
    }

    /** ===================== STORE (DRAFT / SUBMIT) — DENGAN FOTO ===================== */
    public function store(Request $r)
    {
        $isSubmit = $r->has('submit');

        $r->validate([
            'location_id'       => 'required|exists:locations,id',
            'sloc'              => 'nullable|string|max:30',
            'opening_balance'   => 'nullable|integer|min:0',
            'income_total'      => 'nullable|integer|min:0',
            'expense_total'     => 'nullable|integer|min:0',
            'closing_balance'   => 'nullable|integer|min:0',
            'movement_freq'     => 'nullable|integer|min:0',
            'semester'          => 'required|in:1,2',
            'year'              => 'required|integer|min:2000|max:2100',
            'inspector_name'    => 'required|string|max:100',
            'items'             => 'required|array',
            'items.*.rating'    => ($isSubmit?'required':'nullable').'|integer|min:1|max:5',
            'items.*.photo1'    => ($isSubmit?'required':'nullable').'|image|mimes:jpg,jpeg,png,webp|max:4096',
            'items.*.photo2'    => ($isSubmit?'required':'nullable').'|image|mimes:jpg,jpeg,png,webp|max:4096',
            'items.*.extras.*'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $visitDate = $r->semester == 1
            ? sprintf('%d-01-01', $r->year)
            : sprintf('%d-07-01', $r->year);

        DB::transaction(function () use ($r, $isSubmit, $visitDate, &$inspection) {

            $inspection = Inspection::create([
                'checklist_id'     => (int) Checklist::where('is_active', true)->value('id'),
                'location_id'      => (int) $r->location_id,
                'sloc'             => $r->sloc,
                'opening_balance'  => $r->opening_balance,
                'income_total'     => $r->income_total,
                'expense_total'    => $r->expense_total,
                'closing_balance'  => $r->closing_balance,
                'movement_freq'    => $r->movement_freq,
                'semester'         => (int)$r->semester,
                'year'             => (int)$r->year,
                'visited_at'       => $visitDate,
                'inspector_name'   => $r->inspector_name,
                'status'           => $isSubmit ? 'submitted' : 'draft',
                'overall_note'     => $r->overall_note,
            ]);

            $sum = 0; $count = 0;

            foreach ($r->items as $itemId => $payload) {
                $rating = isset($payload['rating']) ? (int)$payload['rating'] : null;

                $path1 = !empty($payload['photo1'])
                    ? $payload['photo1']->store('inspection/'.$inspection->id, 'public') : null;
                $path2 = !empty($payload['photo2'])
                    ? $payload['photo2']->store('inspection/'.$inspection->id, 'public') : null;

                $ii = InspectionItem::create([
                    'inspection_id'     => $inspection->id,
                    'checklist_item_id' => (int)$itemId,
                    'rating'            => $rating,
                    'note'              => $payload['note'] ?? null,
                    'photo_path_1'      => $path1,
                    'photo_path_2'      => $path2,
                ]);

                if (!empty($payload['extras'])) {
                    foreach ((array)$payload['extras'] as $ex) {
                        if ($ex) {
                            $px = $ex->store('inspection/'.$inspection->id, 'public');
                            InspectionPhoto::create([
                                'inspection_item_id' => $ii->id,
                                'photo_path' => $px,
                            ]);
                        }
                    }
                }

                if ($rating) { $sum += $rating; $count++; }
            }

            if ($count > 0) {
                $inspection->overall_score = round($sum / $count, 2);
                $inspection->save();
            }

            if ($isSubmit) {
                $missing = InspectionItem::where('inspection_id', $inspection->id)
                    ->whereNotNull('rating')
                    ->where(function($q){
                        $q->whereNull('photo_path_1')->orWhereNull('photo_path_2');
                    })->count();

                if ($missing > 0) {
                    throw ValidationException::withMessages([
                        'items' => 'Gagal kirim: setiap butir yang dinilai wajib punya Foto 1 & Foto 2.'
                    ]);
                }
            }
        });

        return $isSubmit
            ? $this->export($inspection)
            : redirect()->route('inspections.edit', $inspection)->with('ok','Draft tersimpan. Kamu bisa lanjutkan edit.');
    }

    /** ===================== EDIT / UPDATE (DENGAN FOTO) ===================== */
    public function edit(Inspection $inspection)
    {
        if ($inspection->status !== 'draft') abort(403, 'Hanya draft yang dapat diedit.');
        $inspection->load(['items.photos','location']);
        $checklist = Checklist::with(['categories.items'])->findOrFail($inspection->checklist_id);
        $locations = Location::orderBy('code')->get();
        $existing  = $inspection->items->keyBy('checklist_item_id');
        return view('inspections.create', compact('checklist','locations','existing','inspection'));
    }

    public function update(Request $r, Inspection $inspection)
    {
        if ($inspection->status !== 'draft') abort(403, 'Hanya draft yang dapat diedit.');
        $isSubmit = $r->has('submit');

        $r->validate([
            'location_id'       => 'required|exists:locations,id',
            'sloc'              => 'nullable|string|max:30',
            'opening_balance'   => 'nullable|integer|min:0',
            'income_total'      => 'nullable|integer|min:0',
            'expense_total'     => 'nullable|integer|min:0',
            'closing_balance'   => 'nullable|integer|min:0',
            'movement_freq'     => 'nullable|integer|min:0',
            'semester'          => 'required|in:1,2',
            'year'              => 'required|integer|min:2000|max:2100',
            'inspector_name'    => 'required|string|max:100',
            'items'             => 'required|array',
            'items.*.rating'    => ($isSubmit?'required':'nullable').'|integer|min:1|max:5',
            'items.*.photo1'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'items.*.photo2'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'items.*.extras.*'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $visitDate = $r->semester == 1
            ? sprintf('%d-01-01', $r->year)
            : sprintf('%d-07-01', $r->year);

        DB::transaction(function () use ($r, $isSubmit, $visitDate, $inspection) {

            $inspection->update([
                'location_id'      => (int)$r->location_id,
                'sloc'             => $r->sloc,
                'opening_balance'  => $r->opening_balance,
                'income_total'     => $r->income_total,
                'expense_total'    => $r->expense_total,
                'closing_balance'  => $r->closing_balance,
                'movement_freq'    => $r->movement_freq,
                'semester'         => (int)$r->semester,
                'year'             => (int)$r->year,
                'visited_at'       => $visitDate,
                'inspector_name'   => $r->inspector_name,
                'overall_note'     => $r->overall_note,
                'status'           => $isSubmit ? 'submitted' : 'draft',
            ]);

            $sum = 0; $count = 0;

            foreach ($r->items as $itemId => $payload) {
                $row = InspectionItem::firstOrCreate(
                    ['inspection_id'=>$inspection->id, 'checklist_item_id'=>(int)$itemId],
                    []
                );

                if (!empty($payload['photo1'])) {
                    $row->photo_path_1 = $payload['photo1']->store('inspection/'.$inspection->id, 'public');
                }
                if (!empty($payload['photo2'])) {
                    $row->photo_path_2 = $payload['photo2']->store('inspection/'.$inspection->id, 'public');
                }

                $row->rating = isset($payload['rating']) ? (int)$payload['rating'] : null;
                $row->note   = $payload['note'] ?? null;
                $row->save();

                if (!empty($payload['extras'])) {
                    foreach ((array)$payload['extras'] as $ex) {
                        if ($ex) {
                            $px = $ex->store('inspection/'.$inspection->id, 'public');
                            InspectionPhoto::create([
                                'inspection_item_id' => $row->id,
                                'photo_path' => $px,
                            ]);
                        }
                    }
                }

                if ($row->rating) { $sum += $row->rating; $count++; }
            }

            if ($count > 0) {
                $inspection->overall_score = round($sum / $count, 2);
                $inspection->save();
            }

            if ($isSubmit) {
                $missing = InspectionItem::where('inspection_id',$inspection->id)
                    ->whereNotNull('rating')
                    ->where(function($q){
                        $q->whereNull('photo_path_1')->orWhereNull('photo_path_2');
                    })->count();

                if ($missing > 0) {
                    throw ValidationException::withMessages([
                        'items' => 'Gagal kirim: setiap butir yang dinilai wajib punya Foto 1 & Foto 2.'
                    ]);
                }
            }
        });

        return $isSubmit ? $this->export($inspection) : back()->with('ok','Draft diperbarui.');
    }

    /** ===================== SHOW ===================== */
    public function show(Inspection $inspection)
    {
        $inspection->load(['items.item','items.photos','location']);
        return view('inspections.show', compact('inspection'));
    }

    /** ===================== EXPORT (PDF) ===================== */
    public function export(Inspection $inspection)
    {
        $inspection->load(['items.item','items.photos','location','checklist.categories.items']);

        $ratingLabels = [
            1 => 'Sangat Tidak Sesuai',
            2 => 'Tidak Sesuai',
            3 => 'Cukup Sesuai',
            4 => 'Sesuai',
            5 => 'Sangat Sesuai',
        ];

        $avg  = optional($inspection->items->pluck('rating')->filter())->avg();
        $avg  = $avg ? round($avg,2) : null;
        $percent = $avg !== null ? (int) round($avg * 20) : null;

        $statusLabel = null;
        if ($percent !== null) {
            if ($percent <= 50)       $statusLabel = 'Butuh Perbaikan Segera';
            elseif ($percent <= 70)   $statusLabel = 'Butuh Perbaikan Sementara';
            else                      $statusLabel = 'Masih Aman';
        }

        $movementGrade = null; $movementGradeColor = null;
        if (!is_null($inspection->movement_freq)) {
            $f = (int)$inspection->movement_freq;
            if     ($f >= 4) { $movementGrade = 'Grade 1'; $movementGradeColor = '#16a34a'; }
            elseif ($f >= 2) { $movementGrade = 'Grade 2'; $movementGradeColor = '#f59e0b'; }
            else             { $movementGrade = 'Grade 3'; $movementGradeColor = '#dc2626'; }
        }

        $pdf = Pdf::loadView('inspections.pdf', [
            'inspection'          => $inspection,
            'ratingLabels'        => $ratingLabels,
            'avgScore'            => $avg,
            'percentScore'        => $percent,
            'statusLabel'         => $statusLabel,
            'movementGrade'       => $movementGrade,
            'movementGradeColor'  => $movementGradeColor,
            'generatedAt'         => now(),
        ])->setPaper('a4','portrait');

        $filename = 'Laporan_Inspeksi_'.$inspection->id.'_'.now()->format('Ymd_His').'.pdf';
        return $pdf->download($filename);
    }

    /** ===================== DESTROY (HAPUS DRAFT) ===================== */
    public function destroy(Inspection $inspection)
    {
        if ($inspection->status !== 'draft') {
            abort(403, 'Hanya draft yang dapat dihapus.');
        }
        Storage::disk('public')->deleteDirectory('inspection/'.$inspection->id);
        $inspection->delete();

        return redirect()->route('inspections.drafts')->with('ok','Draft dihapus.');
    }

    /** ===================== MENU: FORMULIR MENTAH (PDF kosong) ===================== */
    public function blankForm()
    {
        $checklist = Checklist::with(['categories.items'])
            ->where('is_active', true)->firstOrFail();
        $locations = Location::orderBy('code')->get();

        return view('inspections.blank', compact('checklist','locations'));
    }

    public function blankExport(Request $r)
    {
        $r->validate([
            'location_id'    => 'nullable|exists:locations,id',
            'sloc'           => 'nullable|string|max:30',
            'semester'       => 'nullable|in:1,2',
            'year'           => 'nullable|integer|min:2000|max:2100',
            'inspector_name' => 'nullable|string|max:100',
        ]);

        $checklist = Checklist::with(['categories.items'])
            ->where('is_active', true)->firstOrFail();

        $plant = '-';
        if ($r->filled('location_id')) {
            $loc = Location::find($r->location_id);
            if ($loc) $plant = ($loc->code ? $loc->code.' — ' : '').$loc->name;
        }

        $meta = [
            'plant'       => $plant,
            'sloc'        => $r->sloc ?: '-',
            'semester'    => $r->semester ? 'Semester '.$r->semester : '-',
            'year'        => $r->year ?: '-',
            'inspector'   => $r->inspector_name ?: '-',
            'generatedAt' => now(),
        ];

        $pdf = Pdf::loadView('inspections.blank_pdf', [
            'checklist' => $checklist,
            'meta'      => $meta,
        ])->setPaper('a4','portrait');

        return $pdf->download('Form_Inspeksi_Mentah_'.now()->format('Ymd_His').'.pdf');
    }

    /** ===================== FORM TANPA FOTO (ISI & SIMPAN) ===================== */
    public function createNoPhoto()
    {
        $checklist  = Checklist::with(['categories.items'])
                        ->where('is_active', true)->firstOrFail();
        $locations  = Location::orderBy('code')->get();
        $existing   = collect();
        $inspection = null;

        // pakai view khusus tanpa komponen foto
        return view('inspections.create_nophoto', compact('checklist','locations','existing','inspection'));
    }

    public function storeNoPhoto(Request $r)
    {
        $isSubmit = $r->has('submit');

        $r->validate([
            'location_id'       => 'required|exists:locations,id',
            'sloc'              => 'nullable|string|max:30',
            'opening_balance'   => 'nullable|integer|min:0',
            'income_total'      => 'nullable|integer|min:0',
            'expense_total'     => 'nullable|integer|min:0',
            'closing_balance'   => 'nullable|integer|min:0',
            'movement_freq'     => 'nullable|integer|min:0',
            'semester'          => 'required|in:1,2',
            'year'              => 'required|integer|min:2000|max:2100',
            'inspector_name'    => 'required|string|max:100',
            'items'             => 'required|array',
            'items.*.rating'    => ($isSubmit?'required':'nullable').'|integer|min:1|max:5',
            // TIDAK ADA VALIDASI FOTO
        ]);

        $visitDate = $r->semester == 1
            ? sprintf('%d-01-01', $r->year)
            : sprintf('%d-07-01', $r->year);

        DB::transaction(function () use ($r, $isSubmit, $visitDate, &$inspection) {

            $inspection = Inspection::create([
                'checklist_id'     => (int) Checklist::where('is_active', true)->value('id'),
                'location_id'      => (int) $r->location_id,
                'sloc'             => $r->sloc,
                'opening_balance'  => $r->opening_balance,
                'income_total'     => $r->income_total,
                'expense_total'    => $r->expense_total,
                'closing_balance'  => $r->closing_balance,
                'movement_freq'    => $r->movement_freq,
                'semester'         => (int)$r->semester,
                'year'             => (int)$r->year,
                'visited_at'       => $visitDate,
                'inspector_name'   => $r->inspector_name,
                'status'           => $isSubmit ? 'submitted' : 'draft',
                'overall_note'     => $r->overall_note,
            ]);

            $sum = 0; $count = 0;

            foreach ($r->items as $itemId => $payload) {
                $rating = isset($payload['rating']) ? (int)$payload['rating'] : null;

                // simpan item TANPA foto
                InspectionItem::create([
                    'inspection_id'     => $inspection->id,
                    'checklist_item_id' => (int)$itemId,
                    'rating'            => $rating,
                    'note'              => $payload['note'] ?? null,
                    'photo_path_1'      => null,
                    'photo_path_2'      => null,
                ]);

                if ($rating) { $sum += $rating; $count++; }
            }

            if ($count > 0) {
                $inspection->overall_score = round($sum / $count, 2);
                $inspection->save();
            }

            // TIDAK ADA cek wajib 2 foto
        });

        return $isSubmit
            ? $this->export($inspection)
            : redirect()->route('inspections.edit_nophoto', $inspection)->with('ok','Draft (tanpa foto) tersimpan.');
    }

    public function editNoPhoto(Inspection $inspection)
    {
        if ($inspection->status !== 'draft') abort(403, 'Hanya draft yang dapat diedit.');
        $inspection->load(['items','location']);
        $checklist = Checklist::with(['categories.items'])->findOrFail($inspection->checklist_id);
        $locations = Location::orderBy('code')->get();
        $existing  = $inspection->items->keyBy('checklist_item_id');

        return view('inspections.create_nophoto', compact('checklist','locations','existing','inspection'));
    }

    public function updateNoPhoto(Request $r, Inspection $inspection)
    {
        if ($inspection->status !== 'draft') abort(403, 'Hanya draft yang dapat diedit.');
        $isSubmit = $r->has('submit');

        $r->validate([
            'location_id'       => 'required|exists:locations,id',
            'sloc'              => 'nullable|string|max:30',
            'opening_balance'   => 'nullable|integer|min:0',
            'income_total'      => 'nullable|integer|min:0',
            'expense_total'     => 'nullable|integer|min:0',
            'closing_balance'   => 'nullable|integer|min:0',
            'movement_freq'     => 'nullable|integer|min:0',
            'semester'          => 'required|in:1,2',
            'year'              => 'required|integer|min:2000|max:2100',
            'inspector_name'    => 'required|string|max:100',
            'items'             => 'required|array',
            'items.*.rating'    => ($isSubmit?'required':'nullable').'|integer|min:1|max:5',
            // TIDAK ADA VALIDASI FOTO
        ]);

        $visitDate = $r->semester == 1
            ? sprintf('%d-01-01', $r->year)
            : sprintf('%d-07-01', $r->year);

        DB::transaction(function () use ($r, $isSubmit, $visitDate, $inspection) {

            $inspection->update([
                'location_id'      => (int)$r->location_id,
                'sloc'             => $r->sloc,
                'opening_balance'  => $r->opening_balance,
                'income_total'     => $r->income_total,
                'expense_total'    => $r->expense_total,
                'closing_balance'  => $r->closing_balance,
                'movement_freq'    => $r->movement_freq,
                'semester'         => (int)$r->semester,
                'year'             => (int)$r->year,
                'visited_at'       => $visitDate,
                'inspector_name'   => $r->inspector_name,
                'overall_note'     => $r->overall_note,
                'status'           => $isSubmit ? 'submitted' : 'draft',
            ]);

            $sum = 0; $count = 0;

            foreach ($r->items as $itemId => $payload) {
                $row = InspectionItem::firstOrCreate(
                    ['inspection_id'=>$inspection->id, 'checklist_item_id'=>(int)$itemId],
                    []
                );

                // TIDAK mengubah kolom foto
                $row->rating = isset($payload['rating']) ? (int)$payload['rating'] : null;
                $row->note   = $payload['note'] ?? null;
                $row->save();

                if ($row->rating) { $sum += $row->rating; $count++; }
            }

            if ($count > 0) {
                $inspection->overall_score = round($sum / $count, 2);
                $inspection->save();
            }

            // TIDAK ADA cek wajib foto
        });

        return $isSubmit ? $this->export($inspection) : back()->with('ok','Draft (tanpa foto) diperbarui.');
    }
}

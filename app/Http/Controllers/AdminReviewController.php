<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(): View { return view('admin.reviews.index', ['reviews' => Review::orderBy('sort_order')->latest()->paginate(20)]); }

    public function create(): View { return view('admin.reviews.form', ['review' => new Review(), 'mode' => 'create']); }

    public function store(Request $request): RedirectResponse
    {
        $review = Review::create($this->validated($request) + ['is_published' => false, 'is_placeholder' => false]);
        $this->auditLogger->record($request, 'review.created', $review);
        return redirect()->route('admin.reviews.index')->with('success', 'Review created as unpublished.');
    }

    public function edit(Review $review): View { return view('admin.reviews.form', ['review' => $review, 'mode' => 'edit']); }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $review->update($this->validated($request) + ['is_published' => false]);
        $this->auditLogger->record($request, 'review.updated', $review);
        return redirect()->route('admin.reviews.index')->with('success', 'Review updated and returned to moderation.');
    }

    public function toggle(Request $request, Review $review): RedirectResponse
    {
        if ($review->is_placeholder && ! $review->is_published) return back()->withErrors(['review' => 'Placeholder reviews cannot be published as customer testimonials.']);
        $review->update(['is_published' => ! $review->is_published]);
        $this->auditLogger->record($request, 'review.moderated', $review);
        return back()->with('success', 'Review publication state updated.');
    }

    public function destroy(Request $request, Review $review): RedirectResponse
    {
        $review->delete();
        $this->auditLogger->record($request, 'review.deleted', null, ['review_id' => $review->id]);
        return back()->with('success', 'Review removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_name' => ['required', 'string', 'max:120'], 'review_text' => ['required', 'string', 'max:2000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'], 'service_name' => ['nullable', 'string', 'max:120'],
            'source' => ['nullable', 'string', 'max:120'], 'published_on' => ['nullable', 'date'], 'sort_order' => ['required', 'integer', 'min:0'],
        ]);
    }
}

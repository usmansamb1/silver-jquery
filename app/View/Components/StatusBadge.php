<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\ApprovalStatus;
use App\Models\StepStatus;

class StatusBadge extends Component
{
    /**
     * The status code or status model
     */
    public $status;

    /**
     * Additional badge classes
     */
    public $class;

    /**
     * Badge size (sm, md, lg)
     */
    public $size;

    /**
     * Badge text
     */
    public $text;

    /**
     * Status model class
     */
    public $model;

    /**
     * Create a new component instance.
     *
     * @param mixed $status Status code string or status model object
     * @param string|null $class Additional classes
     * @param string $size Badge size (sm, md, lg)
     * @param string|null $text Override text to display
     * @param string $model Status model class name (ApprovalStatus or StepStatus)
     */
    public function __construct($status, $class = null, $size = 'md', $text = null, $model = 'ApprovalStatus')
    {
        $this->status = $status;
        $this->class = $class;
        $this->size = $size;
        $this->text = $text;
        $this->model = $model;
    }

    /**
     * Get the status code
     */
    public function getStatusCode()
    {
        if (is_string($this->status)) {
            return $this->status;
        }

        if (is_object($this->status) && method_exists($this->status, 'getStatusCode')) {
            return $this->status->getStatusCode();
        }

        if (is_object($this->status) && property_exists($this->status, 'code')) {
            return $this->status->code;
        }

        if (is_object($this->status) && property_exists($this->status, 'status')) {
            return $this->status->status;
        }

        return 'unknown';
    }

    /**
     * Get the status name
     */
    public function getStatusName()
    {
        // If text is provided, use it
        if ($this->text) {
            return $this->text;
        }

        // If status is an object with a name
        if (is_object($this->status) && property_exists($this->status, 'name')) {
            return $this->status->name;
        }

        // If status is an object with getStatusName method
        if (is_object($this->status) && method_exists($this->status, 'getStatusName')) {
            return $this->status->getStatusName();
        }

        // If status is an object with status property
        if (is_object($this->status) && property_exists($this->status, 'status')) {
            return ucfirst($this->status->status);
        }

        // Default: capitalize the status code
        return ucfirst($this->getStatusCode());
    }

    /**
     * Get the status color
     */
    public function getStatusColor()
    {
        // If status is an object with a color
        if (is_object($this->status) && property_exists($this->status, 'color')) {
            return $this->status->color;
        }

        // If status is an object with getStatusColor method
        if (is_object($this->status) && method_exists($this->status, 'getStatusColor')) {
            return $this->status->getStatusColor();
        }

        // Get from model
        $modelClass = "\\App\\Models\\{$this->model}";
        $statusCode = $this->getStatusCode();

        // Try to find from database first
        if (class_exists($modelClass)) {
            $statusModel = $modelClass::where('code', $statusCode)->first();
            if ($statusModel && isset($statusModel->color)) {
                return $statusModel->color;
            }
        }

        // Fallback colors based on common status codes
        return match ($statusCode) {
            'pending', 'in_progress' => '#FFA500',  // Orange
            'approved', 'completed' => '#28a745',   // Green
            'rejected', 'cancelled' => '#dc3545',   // Red
            'transferred' => '#0d6efd',             // Blue
            'skipped' => '#6c757d',                 // Gray
            default => '#6c757d'                    // Default Gray
        };
    }

    /**
     * Get the CSS class for the badge based on status
     */
    public function getBadgeClass()
    {
        $sizeClass = match ($this->size) {
            'sm' => 'badge-sm px-2 py-1',
            'lg' => 'badge-lg px-3 py-2 fs-6',
            default => 'px-2 py-1'
        };

        $statusClass = match ($this->getStatusCode()) {
            'pending', 'in_progress' => 'bg-warning',
            'approved', 'completed' => 'bg-success',
            'rejected', 'cancelled' => 'bg-danger',
            'transferred' => 'bg-primary',
            'skipped' => 'bg-secondary',
            default => 'bg-secondary'
        };

        return "badge {$statusClass} {$sizeClass} " . $this->class;
    }

    /**
     * Get the badge style attributes
     */
    public function getBadgeStyle()
    {
        $color = $this->getStatusColor();
        return "background-color: {$color} !important;";
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.status-badge');
    }
} 
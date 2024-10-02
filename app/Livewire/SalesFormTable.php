<?php

namespace App\Livewire;

use App\Models\Sale;
use Livewire\Component;

class SalesFormTable extends Component
{
    public $quantity;
    public $unit_cost;
    public $total_cost;
    public $shipping_cost = 10; // Default shipping cost
    public $profit_margin = 0.25; // Default profit margin for Gold Coffee
    public $selling_price;

    public $product_name = 'Gold Coffee'; // Fixed product name for this component

    public $items = [];

    protected $rules = [
        'quantity' => 'required|integer|min:1', // Ensure quantity is a positive integer
        'unit_cost' => 'required|numeric|min:0', // Ensure unit cost is a valid number
    ];

    public function mount()
    {
        // Load existing sales records into the table on component mount
        $this->items = Sale::all()->toArray();
    }

    public function submit()
    {
        // Validate the input
        $this->validate();

        // Calculate total cost
        $this->total_cost = $this->quantity * $this->unit_cost;

        // Calculate selling price using profit margin and shipping cost
        $this->selling_price = round(($this->total_cost / (1 - $this->profit_margin)) + $this->shipping_cost, 2);

        // Save the sale to the database
        $sale = Sale::create([
            'product_name' => $this->product_name,
            'quantity' => $this->quantity,
            'unit_cost' => $this->unit_cost,
            'profit_margin' => $this->profit_margin,
            'shipping_cost' => $this->shipping_cost,
            'total_cost' => $this->total_cost,
            'selling_price' => $this->selling_price,
        ]);

        // Update the items table with the new sale
        $this->items[] = $sale->toArray();

        // Reset form fields for a new entry
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['quantity', 'unit_cost']);

        $this->quantity = null;
        $this->unit_cost = null;
    }

    public function getSellingPriceProperty()
    {
        // Calculate total cost
        $total_cost = $this->quantity * $this->unit_cost;

        // Calculate selling price using profit margin and shipping cost
        return $total_cost > 0 ? round(($total_cost / (1 - $this->profit_margin)) + $this->shipping_cost, 2) : 0;
    }

    public function render()
    {
        return view('livewire.sales-form-table');
    }
}
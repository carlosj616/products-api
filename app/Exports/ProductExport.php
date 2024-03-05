<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    protected $productos;

    public function __construct(Collection $productos)
    {
        $this->productos = $productos;
    }

    public function collection()
    {
        return $this->productos->map(function ($producto) {
            return [
                'id' => $producto['id'],
                'nombre' => $producto['name'],
                'precio' => $producto['price'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Precio',
        ];
    }
}

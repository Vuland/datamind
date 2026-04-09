<?php

declare(strict_types=1);

namespace Modules\Import\Enums;

enum SchemaCollectionKey: string
{
    case Manufacturers = 'manufacturers';
    case Suppliers = 'suppliers';
    case Warehouses = 'warehouses';
    case Products = 'products';
    case Clients = 'clients';
}


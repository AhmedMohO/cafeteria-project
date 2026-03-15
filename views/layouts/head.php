  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Cafeteria' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Nunito', sans-serif; background-color: #f8f9fa; }
    .product-card { cursor: pointer; transition: transform 0.15s, box-shadow 0.15s; border: 2px solid transparent !important; }
    .product-card:hover { transform: translateY(-3px); box-shadow: 0 6px 18px rgba(0,0,0,0.1) !important; border-color: #ffc107 !important; }
    .product-card.selected { border-color: #ffc107 !important; background-color: #fffbf0 !important; }
    .product-icon { width: 100%; aspect-ratio: 4 / 3; border-radius: 12px; background: #fff7db; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .product-thumb { width: 100%; height: 100%; object-fit: cover; display: block; }
    .cart-item-thumb { width: 42px; height: 42px; border-radius: 10px; object-fit: cover; flex-shrink: 0; display: block; }
    .latest-order-thumb { width: 24px; height: 24px; border-radius: 999px; object-fit: cover; display: block; }
    .order-item-thumb { width: 72px; height: 72px; border-radius: 16px; object-fit: cover; display: block; margin: 0 auto 0.5rem; box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
    .product-image-fallback { background: #e9ecef; color: #6c757d; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; }
    .badge-price { background:#fff3cd; color:#856404; font-size:0.75rem; border-radius:20px; padding:2px 10px; display:inline-block; }
    .cart-panel { position: sticky; top: 20px; }
    .table th { background-color: #f1f3f5; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em; }
    .status-processing { background:#fff3cd; color:#856404; }
    .status-delivery { background:#d1ecf1; color:#0c5460; }
    .status-done { background:#d4edda; color:#155724; }
    .section-title { font-size:1.5rem; font-weight:800; color:#212529; }
    .fw-800 { font-weight:800 !important; }
  </style>
  </head>
  <body class="bg-light" style="font-family:'Nunito',sans-serif;">

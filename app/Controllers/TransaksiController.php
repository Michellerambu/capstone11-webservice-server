<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\RajaOngkirService;
use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class TransaksiController extends BaseController
{
    protected $cart;
    protected $transactionModel;
    protected $transactionDetailModel;

    public function __construct()
    {
        helper(['number', 'form']);

        $this->cart = service('cart');

        $this->transactionModel = new TransactionModel();
        $this->transactionDetailModel = new TransactionDetailModel();
    }

    public function index()
    {
        $data = [
            'items' => $this->cart->contents(),
            'total' => $this->cart->total()
        ];

        return view('v_keranjang', $data);
    }

    public function cart_add()
    {
        $this->cart->insert([
            'id'      => $this->request->getPost('id'),
            'qty'     => 1,
            'price'   => $this->request->getPost('harga'),
            'name'    => $this->request->getPost('nama'),
            'options' => [
                'foto' => $this->request->getPost('foto')
            ]
        ]);

        session()->setFlashdata(
            'success',
            'Produk berhasil ditambahkan ke keranjang.
            <a href="' . base_url('keranjang') . '">Lihat</a>'
        );

        return redirect()->to(base_url('/'));
    }

    public function cart_edit()
    {
        $i = 1;

        foreach ($this->cart->contents() as $item) {

            $qty = $this->request->getPost('qty' . $i++);

            $this->cart->update([
                'rowid' => $item['rowid'],
                'qty'   => $qty
            ]);
        }

        session()->setFlashdata(
            'success',
            'Keranjang berhasil diperbarui'
        );

        return redirect()->to(base_url('keranjang'));
    }

    public function cart_delete($rowid)
    {
        $this->cart->remove($rowid);

        session()->setFlashdata(
            'success',
            'Produk berhasil dihapus dari keranjang'
        );

        return redirect()->to(base_url('keranjang'));
    }

    public function cart_clear()
    {
        $this->cart->destroy();

        session()->setFlashdata(
            'success',
            'Keranjang berhasil dikosongkan'
        );

        return redirect()->to(base_url('keranjang'));
    }

    public function checkout()
    {
        $data = [
            'items' => $this->cart->contents(),
            'total' => $this->cart->total()
        ];

        return view('v_checkout', $data);
    }

    public function destinations()
    {
        $search = $this->request->getGet('q');

        $service = new RajaOngkirService();
        $response = $service->getDestination($search);

        $results = [];
        $data = $response['data'] ?? [];

        foreach ($data as $item) {
            $results[] = [
                'id'   => $item['id'],
                'text' => $item['label']
            ];
        }

        return $this->response->setJSON([
            'results' => $results
        ]);
    }

    public function costs()
    {
        $origin = '64999';
        $destination = $this->request->getGet('destination');
        $weight = '1000';
        $courier = 'jne';

        $service = new RajaOngkirService();

        $response = $service->getCost(
            $origin,
            $destination,
            $weight,
            $courier
        );

        $results = [];
        $data = $response['data'] ?? [];

        foreach ($data as $item) {

            $results[] = [
                'service'     => $item['service'],
                'description' => $item['description'],
                'cost'        => $item['cost'],
                'etd'         => $item['etd']
            ];
        }

        return $this->response->setJSON($results);
    }

    public function buy()
    {
        $transaction = [
            'username'    => $this->request->getPost('username'),
            'alamat'      => $this->request->getPost('alamat'),
            'ongkir'      => $this->request->getPost('ongkir'),
            'total_harga' => $this->request->getPost('total_harga'),
            'status'      => 0
        ];

        $this->transactionModel->insert($transaction);

        $transaction_id = $this->transactionModel->getInsertID();

        foreach ($this->cart->contents() as $item) {

            $detail = [
                'transaction_id' => $transaction_id,
                'product_id'     => $item['id'],
                'jumlah'         => $item['qty'],
                'diskon'         => 0,
                'subtotal_harga' => ($item['price'] * $item['qty'])
            ];

            $this->transactionDetailModel->insert($detail);
        }

        $this->cart->destroy();

        session()->setFlashdata(
            'success',
            'Pesanan berhasil dibuat'
        );

        return redirect()->to(base_url('/'));
    }

    public function history()
    {
        $username = session()->get('username'); 
     
        $transactions = $this->transactionModel->where('username', $username)->findAll();
        $transactionIds = array_column($transactions, 'id');

        $products = $this->transactionDetailModel->getProductsByTransactionIds($transactionIds);

        $data = [
            'username'      => $username,
            'transactions'  => $transactions,
            'products'      => $products
        ]; 

        return view('v_history', $data);
    }
}
<?php

class BorrowController extends \BaseController {

    /**
     * Tampilkan halaman index
     * @return response
     */
    public function index()
    {
        if(Datatable::shouldHandle())
        {
            // Membuat datatable
            return Datatable::collection(Borrow::with('user','book')->get())
                ->showColumns('id')
                ->addColumn('book', function($model) {
                    return $model->book->title;
                })
                ->addColumn('user', function($model) {
                    return $model->user->first_name . ' ' . $model->user->last_name;
                })
                ->addColumn('borrowed_at', function($model) {
                    return $model->created_at->toDateString();
                })
                ->addColumn('returned_at', function($model) {
                    if ($model->returned == 0) {
                        return link_to_route('admin.borrow.return', 'kembalikan', ['id'=>$model->id], ['class'=>'uk-button uk-button-small', 'data-returned'=>'false']);
                    }
                    return '<span data-returned="true">' . $model->updated_at->toDateString() . '</span>';
                })
                ->searchColumns('book', 'user', 'borrowed_at', 'returned_at', 'isReturned')
                ->orderColumns('book', 'user', 'borrowed_at', 'returned_at')
                ->make();
        }

        return View::make('borrow.index')->withTitle('Data Peminjaman');
    }

    /**
     * Mengembalikan buku yang sedang dipinjam
     * @param  int $id
     * @return response
     */
    public function returnBack($id)
    {
        $borrow = Borrow::find($id);
        $borrow->returned = 1;
        $borrow->save();
        return Redirect::route('admin.borrow')
            ->with("successMessage", "Buku " . $borrow->book->title . " berhasil dikembalikan");
    }
}

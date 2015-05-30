<?php

class BooksController extends \BaseController {

	/**
	 * Instantiate a new BooksController
	 */
	public function __construct()
	{
		// Letakan filter regularUser sebelum memanggil fungsi borrow
        $this->beforeFilter('regularUser', array( 'only' => array('borrow') ) );

        // CSRF filter sebelum method destroy
		$this->beforeFilter('csrf', array( 'only' => array('destroy') ) );
	}

	/**
	 * Display a listing of books
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Datatable::shouldHandle())
	    {
	    	// eager load author untuk menghemat query sql
	        return Datatable::collection(Book::with('author')->get())
	            ->showColumns('id', 'title', 'amount', 'stock')
	            // menggunakan closure untuk menampilkan nama penulis dari relasi
	            ->addColumn('author', function ($model) {
                    return $model->author->name;
                })
	            ->addColumn('', function ($model) {
                    $html = '<a href="'.route('admin.books.edit', ['books'=>$model->id]).'" class="uk-button uk-button-small uk-button-link">edit</a> ';
                    $html .= Form::open(array('url' => route('admin.books.destroy', ['books'=>$model->id]), 'method'=>'delete', 'class'=>'uk-display-inline'));
					$html .= Form::submit('delete', array('class' => 'uk-button uk-button-small'));
					$html .= Form::close();
					return $html;
                })
	            ->searchColumns('title', 'amount', 'author')
	            ->orderColumns('title', 'amount', 'author')
	            ->make();
	    }
		return View::make('books.index')->withTitle('Buku');
	}

	/**
	 * Show the form for creating a new book
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('books.create')->withTitle('Tambah Buku');
	}

	/**
	 * Store a newly created book in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Book::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$book = Book::create(Input::except('cover'));

        // isi field cover jika ada cover yang diupload
        if (Input::hasFile('cover')) {
            $uploaded_cover = Input::file('cover');

            // mengambil extension file
            $extension = $uploaded_cover->getClientOriginalExtension();

            // membuat nama file random dengan extension
            $filename = md5(time()) . '.' . $extension;
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';

            // memindahkan file ke folder public/img
            $uploaded_cover->move($destinationPath, $filename);

            // mengisi field cover di book dengan filename yang baru dibuat
            $book->cover = $filename;
            $book->save();
        }

		return Redirect::route('admin.books.index')->with("successMessage", "Berhasil menyimpan $book->title ");;
	}

	/**
	 * Display the specified book.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$book = Book::findOrFail($id);

		return View::make('books.show', compact('book'));
	}

	/**
	 * Show the form for editing the specified book.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$book = Book::find($id);

		return View::make('books.edit', ['book'=>$book])->withTitle("Ubah $book->title");
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$book = Book::findOrFail($id);

		$validator = Validator::make(Input::all(), $book->updateRules());

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

        if (Input::hasFile('cover')) {
            $filename = null;
            $uploaded_cover = Input::file('cover');
            $extension = $uploaded_cover->getClientOriginalExtension();

            // membuat nama file random dengan extension
            $filename = md5(time()) . '.' . $extension;
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';

            // memindahkan file ke folder public/img
            $uploaded_cover->move($destinationPath, $filename);

            // hapus cover lama, jika ada
            if ($book->cover) {
                $old_cover = $book->cover;
                $filepath = public_path() . DIRECTORY_SEPARATOR . 'img'
                    . DIRECTORY_SEPARATOR . $book->cover;

                try {
                    File::delete($filepath);
                } catch (FileNotFoundException $e) {
                    // File sudah dihapus/tidak ada
                }
            }

            // ganti field cover dengan cover yang baru
            $book->cover = $filename;
            $book->save();
        }

		if (!$book->update(Input::except('cover'))) {
            return Redirect::back();
        }

		return Redirect::route('admin.books.index')->with("successMessage", "Berhasil menyimpan $book->title. ");
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if (!Book::destroy($id)) {
            return Redirect::back();
        }

        if (Request::ajax()) {
            return Response::json(array('id' => $id));
        }

		return Redirect::route('admin.books.index')->with('successMessage', 'Buku berhasil dihapus.');
	}

	/**
	 * Borrow book
	 * @param  int $id book id
	 * @return response
	 */
	public function borrow($id)
	{
		$book = Book::findOrFail($id);

		try {
			$book->borrow();
		} catch (BookException $e) {
			return Redirect::back()->with('errorMessage', $e->getMessage());
		}

		return Redirect::back()->with("successMessage", "Anda telah meminjam $book->title");
	}

	/**
	 * Get datatable for books borrow
	 * @return json
	 */
	public function borrowDatatable()
    {
        // eager load author untuk menghemat query sql
        return Datatable::collection(Book::with('author')->get())
            ->showColumns('id', 'title', 'amount', 'stock')
            // menggunakan closure untuk menampilkan nama penulis dari relasi
            ->addColumn('author', function ($model) {
                return $model->author->name;
            })
            // menggunakan helper untuk membuat link
            ->addColumn('', function ($model) {
                return link_to_route('books.borrow', 'Pinjam', ['book'=>$model->id]);
            })
            ->searchColumns('title', 'amount', 'author')
            ->orderColumns('title', 'amount', 'author')
            ->make();
    }

    /**
     * Return specified book
     * @param  int $id book id
     * @return response
     */
    public function returnBack($id)
    {
    	$book = Book::findOrFail($id);
    	$book->returnBack();
    	return Redirect::back()->with("successMessage", "Anda telah mengembalikan $book->title.");
    }

    /**
     * Tampilkan halaman untuk export excel
     * @return response
     */
    public function export()
    {
        return View::make('books.export')->withTitle('Export Buku');
    }

    /**
     * Download excel data buku
     * @return PHPExcel
     */
    public function exportPost()
    {
        // validasi
        $rules = ['author_id'=>'required', 'type'=>'required'];
        $messages = ['author_id.required'=>'Anda belum memilih penulis. Pilih minimal 1 penulis.'];
        $validator = Validator::make(Input::all(), $rules, $messages);
        if ($validator->fails())
        {
            return Redirect::back()->withErrors($validator);
        }

        $books = Book::whereIn('id', Input::get('author_id'))->get();

        $type = Input::get('type');
        switch ($type) {
            case 'xls':
                return $this->exportExcel($books);
                break;

            case 'pdf':
                return $this->exportPdf($books);
                break;

            default:
                break;
        }
    }

    /**
     * Download excel data buku
     * @return PHPExcel
     */
    private function exportExcel($books)
    {
        Excel::create('Data Buku Larapus', function($excel) use ($books) {
            // Set the properties
            $excel->setTitle('Data Buku Larapus')
                  ->setCreator('Rahmat Awaludin');

            $excel->sheet('Data Buku', function($sheet) use ($books) {
                $row = 1;
                $sheet->row($row, array(
                    'Judul',
                    'Jumlah',
                    'Stok',
                    'Penulis'
                ));
                foreach ($books as $book) {
                   $sheet->row(++$row, array(
                    $book->title,
                    $book->amount,
                    $book->stock,
                    $book->author->name
                ));
                }
            });
        })->export('xls');
    }

    /**
     * Download pdf data buku
     * @return Dompdf
     */
    private function exportPdf($books)
    {
        $data['books'] = $books;
        $pdf = PDF::loadView('pdf.books', $data);
        return $pdf->download('books.pdf');
    }

    /**
     * Generate template excel untuk import buku
     * @return PHPExcel
     */
    public function generateExcelTemplate()
    {
        Excel::create('Template Import Buku', function($excel) {
            // Set the properties
            $excel->setTitle('Template Import Buku')
                  ->setCreator('Larapus')
                  ->setCompany('Larapus')
                  ->setDescription('Template import buku untuk Larapus');

            $excel->sheet('Data Buku', function($sheet) {
                $row = 1;
                $sheet->row($row, array(
                    'Judul',
                    'Penulis',
                    'Jumlah'
                ));
            });

        })->export('xls');
    }

    /**
     * Import excel dan simpan ke database
     * @return response
     */
    public function importExcel()
    {
        // Validasi
        $rules = ['excel' => 'required|mimes:xls'];
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
            return Redirect::back()->withErrors($validator)->withInput();

        $excel = Input::file('excel');

        // Ambil sheet pertama
        $excels = Excel::selectSheetsByIndex(0)->load($excel, function($reader) {
            // options, jika ada
        })->get();

        // digunakan untuk menghitung total buku yang masuk
        $counter = 0;

        // rule untuk validasi setiap row pada file excel
        $rowRules = [
            'Judul' => 'required',
            'Penulis' => 'required',
            'Jumlah' => 'required'
        ];

        // Catat semua id buku baru
        $books_id = [];

        // looping setiap baris, mulai dari baris ke 2
        foreach ($excels as $row) {
            // Membuat validasi untuk row di excel
            // Jangan lupa untuk mengubah $row menjadi array
            $validator = Validator::make($row->toArray(), $rowRules);

            // Skip baris ini jika tidak valid, langsung ke baris selanjutnya
            if ($validator->fails()) continue;

            // Cek apakah Penulis sudah terdaftar di database
            $author = Author::where('name', $row['Penulis'])->first();

            // buat penulis jika belum ada
            if (!$author) {
                $author = Author::create(['name'=>$row['Penulis']]);
            }

            // buat buku baru
            $book = Book::create([
                'title' => $row['Judul'],
                'author_id' => $author->id,
                'amount' => $row['Jumlah']
            ]);

            $counter++;
            array_push($books_id, $book->id);
        }

        // redirect ke form jika tidak ada buku yang berhasil diimport
        if ($counter == 0) {
            return Redirect::back()->with("errorMessage", "Tidak ada buku yang berhasil diimport.");;
        }

        // Ambil semua buku yang baru dibuat
        $books = Book::whereIn('id', $books_id)->get();

        // Tampilkan halaman review buku
        return View::make('books.import-review')
            ->with('books', $books)
            ->withTitle('Review Buku');
    }

}
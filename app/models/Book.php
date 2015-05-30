<?php

class Book extends BaseModel {

	// Add your validation rules here
	public static $rules = [
		'title' => 'required|unique:books,title,:id',
        'author_id' => 'required|exists:authors,id',
        'amount'=> 'numeric',
        'cover' => 'image|max:2048'
	];

	// Don't forget to fill this array
	protected $fillable = ['title','author_id','amount'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::updating(function($book)
        {
            $borrowed = DB::table('book_user')
                ->where('book_id', $book->id)
                ->where('returned', 0)
                ->count();
            if ($book->amount < $borrowed) {
                Session::flash('errorMessage', "Jumlah buku $book->title harus >= $borrowed");
                return false;
            }
        });

        self::deleting(function($book)
        {
            $borrowed = DB::table('book_user')
                ->where('book_id', $book->id)
                ->where('returned', 0)
                ->count();
            if ($borrowed > 0) {
                Session::flash('errorMessage', "Buku $book->title masih dipinjam.");
                return false;
            }
        });
    }

    /**
     * Custom attributes
     * @var array
     */
    protected $appends = ['stock'];

    /**
     * Accessor for stock attribute
     * @return int
     */
    public function getStockAttribute()
    {
        $borrowed =  DB::table('book_user')
            ->where('book_id', $this->id)
            ->where('returned', 0)
            ->count();
        $stock = $this->amount - $borrowed;
        return $stock;
    }

    /**
     * Kebalikan relasi One-to-Many relation dengan model Author
     * @return Author
     */
    public function author()
    {
        return $this->belongsTo('Author');
    }

    /**
     * Relasi pivot (Many-to-Many) dengan User
     * @return Collection
     */
    public function users()
    {
        return $this->belongsToMany('User')->withPivot('returned')->withTimestamps();
    }

    /**
     * Attach current user id to book->users
     * @return response
     */
    public function borrow()
    {
        $user = Sentry::getUser();

        // cek apakah buku ini sedang dipinjam oleh user
        if($user->books()->wherePivot('book_id',$this->id)->wherePivot('returned', 0)->count() > 0 ) {
            throw new BookAlreadyBorrowedException("Buku $this->title sedang Anda pinjam.");
        }

        if($this->stock == 0) {
            throw new BookOutOfStockException("Buku $this->title sudah tidak tersedia.");
        }

        return $this->users()->attach($user);
    }

    public function returnBack()
    {
        $user = Sentry::getUser();
        DB::table('book_user')
            ->where('book_id', $this->id)
            ->where('user_id', $user->id)
            ->where('returned', 0)
            ->update(array(
                'returned' => 1,
                'updated_at' => $this->freshTimestamp()
            ));
    }

}
<?php

class Borrow extends BaseModel {
     protected $table = 'book_user';

     /**
      * Relasi One-to-One dengan Book
      * @return Book
      */
    public function book()
    {
        return $this->belongsTo('Book');
    }

    /**
     * Relasi One-to-One dengan User
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

}
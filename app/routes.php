<?php

Route::get('/', 'GuestController@index');
Route::get('datatable/books/borrow', array('as'=>'datatable.books.borrow', 'uses'=>'BooksController@borrowDatatable'));
Route::group(array('before' => 'auth'), function () {
    Route::get('dashboard', array('as'=>'dashboard', 'uses'=>'HomeController@dashboard'));
    Route::get('books', array('as'=>'member.books', 'uses'=>'MemberController@books'));
    Route::get('profile', array('as'=>'member.profile', 'uses'=>'MemberController@profile'));
    Route::get('profile/edit', array('as'=>'member.profile.edit', 'uses'=>'MemberController@editProfile'));
    Route::post('profile', array('as'=>'member.profile.update', 'uses'=>'MemberController@updateProfile'));
    Route::get('editpassword', array('as'=>'home.editpassword', 'uses'=>'HomeController@editPassword'));
    Route::post('updatepassword', array('as'=>'home.updatepassword', 'uses'=>'HomeController@updatePassword'));
    Route::get('books/{book}/borrow', array('as'=>'books.borrow', 'uses'=>'BooksController@borrow'));
    Route::get('books/{book}/return', array('as'=>'books.return', 'uses'=>'BooksController@returnBack'));
    Route::group(array('prefix' => 'admin', 'before' => 'admin'), function()
    {
        Route::resource('authors', 'AuthorsController');
        Route::get('books/export', array('as'=>'admin.books.export', 'uses'=>'BooksController@export'));
        Route::post('books/export-post', array('as'=>'admin.books.exportpost', 'uses'=>'BooksController@exportPost'));
        Route::get('books/template', array('as'=>'admin.books.template', 'uses'=>'BooksController@generateExcelTemplate'));
        Route::post('books/import', array('as'=>'admin.books.import', 'uses'=>'BooksController@importExcel'));
        Route::resource('books', 'BooksController');
        Route::resource('users', 'UsersController');
        Route::get('borrow', array('as'=>'admin.borrow', 'uses'=>'BorrowController@index'));
        Route::get('borrow/return/{id}', array('as'=>'admin.borrow.return', 'uses'=>'BorrowController@returnBack'));
    });
});
Route::get('login', array('as'=>'guest.login', 'uses'=>'GuestController@login'));
Route::post('authenticate', 'HomeController@authenticate');
Route::get('logout', 'HomeController@logout');
Route::get('signup', array('as'=>'guest.signup', 'uses'=>'GuestController@signup'));
Route::post('register', array('as'=>'guest.register', 'uses'=>'GuestController@register'));
Route::get('activate', array('as'=>'user.activate', 'uses'=>'GuestController@activate'));
Route::get('forgot', array('as'=>'guest.forgot', 'uses'=>'GuestController@forgotPassword'));
Route::post('sendresetcode', array('as'=>'guest.sendresetcode', 'uses'=>'GuestController@sendResetCode'));
Route::get('reset', array('as'=>'guest.createnewpassword', 'uses'=>'GuestController@createNewPassword'));
Route::post('reset', array('as'=>'guest.storenewpassword', 'uses'=>'GuestController@storeNewPassword'));

?>
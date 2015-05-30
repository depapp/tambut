 <?php

class MemberController extends BaseController {

    /**
     * Setup filter
     */
    public function __construct()
    {
        // Letakan filter regularUser sebelum memanggil semua method
        $this->beforeFilter('regularUser');
    }

    /**
     * Tampilkan halaman peminjaman buku
     * @return response
     */
    public function books()
    {
        return View::make('books.borrow')->withTitle('Pilih buku');
    }

    /**
     * Tampilkan halaman profile
     * @return response
     */
    public function profile()
    {
    	$user = Sentry::getUser();
    	return View::make('profile.show')->withTitle('Profil')
    	    ->withUser($user);
    }

    /**
     * Tampilkan halaman edit profile
     * @return response
     */
    public function editProfile()
    {
        return View::make('profile.edit')->withTitle('Perbaharui Profil')
                ->withUser(Sentry::getUser());
    }

    /**
     * Ubah profile user
     * @return response
     */
    public function updateProfile()
    {
        $user = Sentry::getUser();
        try
        {
            // Perbaharui data user
            $user->email = Input::get('email');
            $user->first_name = Input::get('first_name');
            $user->last_name = Input::get('last_name');
            $user->save();

            // Simpan data user
            if ($user->save())
            {
                return Redirect::route('member.profile')->with('successMessage', 'Profil berhasil diperbaharui.');
            }
        }
        catch (Cartalyst\Sentry\Users\UserExistsException $e)
        {
            return Redirect::back()->with('errorMessage', $e->getMessage())->withInput();
        }
    }
}
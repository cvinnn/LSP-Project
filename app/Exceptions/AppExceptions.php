<?php

namespace App\Exceptions;

use Exception;

// Exception untuk error peminjaman
class BorrowingException extends Exception
{
}

// Exception untuk error buku
class BookException extends Exception
{
}

// Exception untuk error user
class UserException extends Exception
{
}

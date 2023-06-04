<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Repository\UserRepository;
use Nette;


final class HomePresenter extends Nette\Application\UI\Presenter
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function actionDefault()
    {
        dd("Test");
    }
}

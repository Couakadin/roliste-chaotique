<?php

namespace App\Controller\Admin\Utils;

use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatorTrait
{
    public function __construct(public readonly TranslatorInterface $translator)
    {
    }

    protected function trans(string $trans, array $options = []): string
    {
        return $this->mbUcfirst($this->translator->trans($trans, $options, 'admins'));
    }

    /**
     * ucfirst-like to allow utf-8 characters
     *
     * @param $str
     * @param string $encode
     * @return string
     */
    private function mbUcfirst($str, string $encode = 'UTF-8'): string
    {

        $start = mb_strtoupper(mb_substr($str, 0, 1, $encode), $encode);
        $end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encode), $encode), $encode);

        return $start . $end;
    }
}

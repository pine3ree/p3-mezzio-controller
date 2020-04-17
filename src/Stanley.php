<?php

/**
 * @package     package
 * @subpackage  package-subpackage
 * @author      pine3ree https://github.com/pine3ree
 */

namespace P3\Mezzio\Controller;

/**
 * Class Stanley
 */
class Stanley
{

    public function doSomething(bool $bool): string
    {
        if ($this->isTruthy($bool)) {
            return $this->go($bool);
        }

        return 'false';
    }

    /**
     *
     * @param bool|string $bool
     * @return string
     */
    public function go($bool): string
    {
        return 'gone';
    }

    /**
     *
     * @param bool|string $bool
     * @return bool
     */
    public function isTruthy($bool): bool
    {
        return true;
    }
}

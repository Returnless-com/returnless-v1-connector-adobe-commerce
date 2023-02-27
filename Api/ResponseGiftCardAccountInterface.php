<?php

namespace Returnless\ExtendRestApi\Api;

interface ResponseGiftCardAccountInterface
{
    const DATA_ID = 'id';
    const DATA_GIFTCARD_CODE = 'giftcard_code';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getGiftcardCode();

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id);

    /**
     * @param string $giftcardCode
     * @return $this
     */
    public function setGiftcardCode(string $giftcardCode);
}

<?php
namespace Returnless\Integration\Model\Api;

use Magento\Framework\DataObject;
use Returnless\Integration\Api\ResponseGiftCardAccountInterface;

class ResponseGiftCardAccount extends DataObject implements ResponseGiftCardAccountInterface
{
    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->_getData(self::DATA_ID);
    }

    /**
     * @return string
     */
    public function getGiftcardCode() : string
    {
        return $this->_getData(self::DATA_GIFTCARD_CODE);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id) : ResponseGiftCardAccountInterface
    {
        return $this->setData(self::DATA_ID, $id);
    }

    /**
     * @param string $giftcardCode
     * @return $this
     */
    public function setGiftcardCode(string $giftcardCode) : ResponseGiftCardAccountInterface
    {
        return $this->setData(self::DATA_GIFTCARD_CODE, $giftcardCode);
    }
}

<?php

namespace sima\timecop;

use yii\base\{
    Application,
    BootstrapInterface,
    Component,
    InvalidConfigException
};
use yii\web\{
    Request,
    Session
};
use yii\di\Instance;

/**
 * Класс для смены времени
 */
class TimeFreeze extends Component implements BootstrapInterface
{
    /**
     * @var string Наименование гет-параметра для управления временем.
     */
    public $requestVariable = 'SIMA_TIME_VECTOR';

    /**
     * @var string|Session Сессия.
     */
    public $session = 'session';

    /**
     * @var string|Request Запрос.
     */
    public $request = 'request';

    /**
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->session = Instance::ensure($this->session, Session::class);
        $this->request = Instance::ensure($this->request, Request::class);
    }

    /**
     *
     * @param Application $app
     */
    public function bootstrap($app): void
    {
        if (\function_exists('timecop_freeze')) {
            $timeVector = $this->request->getQueryParam($this->requestVariable);
            if (null !== $timeVector) {
                if ($timeVector === 'reset') {
                    $this->session->remove($this->requestVariable);
                    $timeVector = null;
                } else {
                    $this->session->set($this->requestVariable, $timeVector);
                }
            } else {
                $timeVector = $this->session->get($this->requestVariable);
            }
            if (null !== $timeVector) {
                timecop_freeze((int)$timeVector);
            }
        }
    }
}
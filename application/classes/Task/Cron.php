
<?php defined('SYSPATH') or die('No direct script access.');
 
class Task_Cron extends Minion_Task {
    protected $_options = array(
        'action' => ''
    );

    /**
     * This is a demo task
     *
     * @return null
     */
    protected function _execute(array $params) {
        if( $params['action'] == 'backupLastDays' ) {
            $this->backupLastDays();
        } else if( $params['action'] == 'stillAlive' ) {
            $this->stillAlive();
        } else if( $params['action'] == 'checkTodos' ) {
            $this->checkTodos();
        } else if( $params['action'] == 'createData' ) {
            $this->createData();
        }
    }

    private function stillAlive() {
        $mInstance = new Model_Instance();
//        $mInstance->updateStillAlive(1);
        $mInstance->updateStillAlive(2);
    }

    private function createData() {
//        $mData = new Model_Data();
//        // FIXME code
//        $mData->insertData(1, array(
//            'datetime' => null,
//            'humidity' => $this->f_rand(20, 40, 10),
//            'roomTemperature' => $this->f_rand(18, 25, 10),
//            'tankTemperature' => $this->f_rand(18, 25, 10)
//        ));

        $mData = new Model_Data();
        $mData->insertData(2, array(
            'datetime' => null,
            'humidity' => $this->f_rand(20, 40, 10),
            'roomTemperature' => $this->f_rand(18, 25, 10),
            'tankTemperature' => $this->f_rand(18, 25, 10)
        ));

        $mWater = new Model_WaterTest();
        $mWater->insertWaterTest(2, array(
            'date' => date('Y-m-d'),
            'ph' => $this->f_rand(5, 8, 10),
            'ammonia' => $this->f_rand(0, 7.3, 10),
            'nitrite' => $this->f_rand(0, 3.3, 10),
            'nitrate' => $this->f_rand(0, 110, 10)
        ));
    }

    private function f_rand($min=0,$max=1,$mul=1000000){
        if ($min>$max) return false;
        return mt_rand($min*$mul,$max*$mul)/$mul;
    }
    
    private function checkTodos() {
        $config = Kohana::$config->load('app');
        $mTodo = new Model_Todo();
        $todos = $mTodo->getUncheckedTodos();

        $messageParams = array(
            'base_url' => $config['base_url'],
            'todos' => $todos
        );
        $viewMessage = View::factory( "email/todos" )->set($messageParams);

        $mLog = new Model_Log();

        $message = array(
            'subject' => 'EcoSystem - ToDo\'s',
            'body'    => $viewMessage->render(),
            'from'    => array($config['sender_email'] => 'EcoSystem'),
            'to'      => $config['admin_email']
        );
        $code = Email::send('default', $message['subject'], $message['body'], $message['from'], $message['to'], $mime_type = 'text/html');
        if( ! $code ) {
            $mLog->log( "error", __("Error while sending tasks list.") );
        } else {
            $mLog->log( "info", __("Send tasks list by email") );

        }
    }
    
//    private function backupLastDays() {
//        // TODO loop on users for backup
//        $mInstance = new Model_Instance();
//        // TODO get user for getInstances
//        $instances = $mInstance->getInstances();
//        $mHour = new Model_Hour();
//        $mDay = new Model_Day();
//
//        foreach($instances as $instance) {
//            $lastDaysAvg = $mHour->getLastDaysTempAverage($instance['id_instance']);
//            foreach ($lastDaysAvg as $dayAvg) {
//                $mDay->updateDayAvg($instance['id_instance'], $dayAvg['date'], $dayAvg['avg_room_temp'], $dayAvg['avg_tank_temp']);
//                $mHour->deleteHours($dayAvg['date']);
//            }
//        }
//        $mLog = new Model_Log();
//        $mLog->log( "info", __("Update last days average and delete old data") );
//    }
}
<?php

class cartosi extends CommonDBTM {

   // Should return the localized name of the type
   static function getTypeName($nb = 0) {
      return 'Example Type';
   }

   /**
    * Give localized information about 1 task
    *
    * @param $name of the task
    *
    * @return array of strings
    */
   static function cronInfo($name) {

      switch ($name) {
         case 'SynchroGlpiCartoSi' :
            return ['description' => __('Paulo description for example', 'cartosi')];
      }
      return [];
   }

   /**
    * Execute 1 task manage by the plugin
    *
    * @param $task Object of CronTask class for log / stat
    *
    * @return interger
    *    >0 : done
    *    <0 : to be run again (not finished)
    *     0 : nothing to do
    */
   static function croncartosi($task) {

      $task->log("Example log message from class", 'cartosi');
      usleep(1000000+$r*1000);
      return 1;
   }
}

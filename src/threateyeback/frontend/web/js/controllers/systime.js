var rootScope;
var myApp = angular.module('myApp', []);
myApp.controller('systime', function($scope, $http, $filter) {
  rootScope = $scope;
  $scope.init = function(params) {

$scope.datas = ['Africa/Abidjan','Africa/Accra','Africa/Addis_Ababa',
    'Africa/Asmara','Africa/Bamako','Africa/Bangui','Africa/Banjul',
    'Africa/Bissau','Africa/Blantyre','Africa/Brazzaville','Africa/Bujumbura',
    'Africa/Cairo','Africa/Casablanca','Africa/Maputo','Africa/Maseru',
    'Africa/Dakar','Africa/Dar_es_Salaam','Africa/Djibouti','Africa/Douala',
    'Africa/El_Aaiun','Africa/Freetown','Africa/Gaborone','Africa/Harare',
    'Africa/Johannesburg','Africa/Juba','Africa/Kampala','Africa/Khartoum',
    'Africa/Kigali','Africa/Kinshasa','Africa/Lagos','Africa/Libreville',
    'Africa/Lome','Africa/Luanda','Africa/Algiers','Africa/Lusaka','Africa/Ceuta',
    'Africa/Malabo','Africa/Mbabane','Africa/Conakry','Africa/Lubumbashi',
    'Africa/Mogadishu','Africa/Monrovia','Africa/Nairobi','Africa/Ndjamena',
    'Africa/Niamey','Africa/Nouakchott','Africa/Ouagadougou','Africa/Porto-Novo',
    'Africa/Sao_Tome','Africa/Tripoli','Africa/Tunis','Africa/Windhoek',
    //美洲
    'America/Adak','America/Anchorage','America/Anguilla','America/Antigua',
    'America/Araguaina','America/Argentina/Buenos_Aires','America/Argentina/Catamarca',
    'America/Argentina/Jujuy','America/Argentina/La_Rioja','America/Argentina/Mendoza',
    'America/Argentina/Salta','America/Belem','America/Bahia','America/Bahia_Banderas',
    'America/Argentina/Ushuaia','America/Aruba','America/Asuncion','America/Atikokan',
    'America/Argentina/San_Juan','America/Barbados','America/Argentina/San_Luis',
    'America/Belize','America/Blanc-Sablon','America/Boa_Vista','America/Bogota',
    'America/Boise','America/Cambridge_Bay','America/Campo_Grande','America/Cancun',
    'America/Caracas','America/Cayenne','America/Cayman','America/Chicago',
    'America/Chihuahua','America/Costa_Rica','America/Creston','America/Cuiaba',
    'America/Curacao','America/Danmarkshavn','America/Dawson','America/Dawson_Creek',
    'America/Denver','America/Detroit','America/Dominica','America/Edmonton',
    'America/Eirunepe','America/El_Salvador','America/Fort_Nelson','America/Fortaleza',
    'America/Glace_Bay','America/Godthab','America/Goose_Bay','America/Grand_Turk',
    'America/Grenada','America/Guadeloupe','America/Guatemala','America/Guayaquil',
    'America/Guyana','America/Halifax','America/Havana','America/Hermosillo',
    'America/Indiana/Indianapolis','America/Indiana/Knox','America/Indiana/Marengo',
    'America/Indiana/Tell_City','America/Indiana/Vevay','America/Indiana/Vincennes',
    'America/Inuvik','America/Iqaluit','America/Jamaica','America/Argentina/Cordoba',
    'America/Kentucky/Louisville','America/Kentucky/Monticello','America/Kralendijk',
    'America/Lima','America/Los_Angeles','America/Lower_Princes','America/Maceio',
    'America/Managua','America/Manaus','America/Marigot','America/Martinique',
    'America/Matamoros','America/Mazatlan','America/Menominee','America/Merida',
    'America/Metlakatla','America/Mexico_City','America/Miquelon','America/Moncton',
    'America/Monterrey','America/Montevideo','America/Montserrat','America/Nassau',
    'America/New_York','America/Nipigon','America/Nome','America/Noronha','America/La_Paz',
    'America/North_Dakota/Beulah','America/North_Dakota/Center','America/Tegucigalpa',
    'America/North_Dakota/New_Salem	America/Ojinaga','America/Panama','America/Porto_Velho',
    'America/Pangnirtung','America/Paramaribo','America/Phoenix','America/Indiana/Winamac',
    'America/Port-au-Prince','America/Port_of_Spain','America/Argentina/Tucuman',
    'America/Puerto_Rico','America/Punta_Arenas','America/Rainy_River','America/Juneau',
    'America/Rankin_Inlet','America/Recife','America/Regina','America/Resolute',
    'America/Rio_Branco','America/Santarem','America/Sitka','America/St_Barthelemy',
    'America/St_Johns','America/St_Kitts','America/St_Lucia','America/St_Thomas',
    'America/St_Vincent','America/Swift_Current','America/Indiana/Petersburg',
    'America/Thule','America/Thunder_Bay','America/Tijuana','America/Whitehorse',
    'America/Toronto','America/Tortola','America/Vancouver','America/Yellowknife',
    'America/Winnipeg','America/Yakutat','America/Argentina/Rio_Gallegos',
    //南极洲
    'Antarctica/Syowa','Antarctica/Troll','Antarctica/Vostok','Antarctica/Macquarie',
    'Antarctica/Mawson','Antarctica/McMurdo','Antarctica/Palmer','Antarctica/Rothera',
    'Antarctica/Casey','Antarctica/Davis','Antarctica/DumontDUrville',
    // 北极
    'Arctic/Longyearbyen',
    // 亚洲
    'Asia/Aden','Asia/Almaty','Asia/Amman','Asia/Anadyr','Asia/Dushanbe','Asia/Dili',
    'Asia/Aqtau','Asia/Aqtobe','Asia/Ashgabat','Asia/Atyrau','Asia/Krasnoyarsk',
    'Asia/Baghdad','Asia/Bahrain','Asia/Baku','Asia/Bangkok','Asia/Dhaka',
    'Asia/Barnaul','Asia/Beirut','Asia/Bishkek','Asia/Brunei','Asia/Kuwait',
    'Asia/Chita','Asia/Choibalsan','Asia/Colombo','Asia/Damascus','Asia/Dubai',
    'Asia/Famagusta','Asia/Gaza','Asia/Hebron','Asia/Ho_Chi_Minh','Asia/Kuching',
    'Asia/Hong_Kong','Asia/Hovd','Asia/Irkutsk','Asia/Jakarta','Asia/Kuala_Lumpur',
    'Asia/Jayapura','Asia/Jerusalem','Asia/Kabul','Asia/Kamchatka','Asia/Manila',
    'Asia/Karachi','Asia/Kathmandu','Asia/Khandyga','Asia/Kolkata','Asia/Makassar',
    'Asia/Macau','Asia/Magadan','Asia/Novosibirsk','Asia/Novokuznetsk','Asia/Tokyo',
    'Asia/Muscat','Asia/Nicosia','Asia/Pontianak','Asia/Thimphu','Asia/Ust-Nera',
    'Asia/Omsk','Asia/Oral','Asia/Phnom_Penh','Asia/Ulaanbaatar','Asia/Urumqi',
    'Asia/Pyongyang','Asia/Qatar','Asia/Qyzylorda','Asia/Riyadh','Asia/Tehran',
    'Asia/Sakhalin','Asia/Samarkand','Asia/Seoul','Asia/Shanghai','Asia/Tbilisi',
    'Asia/Singapore','Asia/Srednekolymsk','Asia/Taipei','Asia/Tashkent',
    'Asia/Vientiane','Asia/Vladivostok','Asia/Yakutsk','Asia/Yangon',
    'Asia/Yekaterinburg','Asia/Yerevan','Asia/Tomsk',

    // 大西洋
    'Atlantic/Azores','Atlantic/Bermuda','Atlantic/Canary','Atlantic/Cape_Verde',
    'Atlantic/Faroe','Atlantic/Madeira','Atlantic/Reykjavik','Atlantic/South_Georgia',
    'Atlantic/St_Helena','Atlantic/Stanley',
    // 澳洲
    'Australia/Adelaide','Australia/Brisbane','Australia/Broken_Hill','Australia/Currie',
    'Australia/Darwin','Australia/Eucla','Australia/Hobart','Australia/Lindeman',
    'Australia/Lord_Howe','Australia/Melbourne','Australia/Perth','Australia/Sydney',
    // 欧洲
    'Europe/Amsterdam','Europe/Andorra','Europe/Astrakhan','Europe/Athens',
    'Europe/Belgrade','Europe/Berlin','Europe/Bratislava','Europe/Brussels',
    'Europe/Bucharest','Europe/Budapest','Europe/Busingen','Europe/Chisinau',
    'Europe/Copenhagen','Europe/Dublin','Europe/Gibraltar','Europe/Guernsey',
    'Europe/Helsinki','Europe/Isle_of_Man','Europe/Istanbul','Europe/Jersey',
    'Europe/Kaliningrad','Europe/Kiev','Europe/Kirov','Europe/Lisbon',
    'Europe/Ljubljana','Europe/London','Europe/Luxembourg','Europe/Madrid',
    'Europe/Malta','Europe/Mariehamn','Europe/Minsk','Europe/Monaco',
    'Europe/Moscow','Europe/Oslo','Europe/Paris','Europe/Podgorica',
    'Europe/Prague','Europe/Riga','Europe/Rome','Europe/Samara',
    'Europe/San_Marino','Europe/Sarajevo','Europe/Saratov','Europe/Simferopol',
    'Europe/Skopje','Europe/Sofia','Europe/Stockholm','Europe/Tallinn',
    'Europe/Tirane','Europe/Ulyanovsk','Europe/Uzhgorod','Europe/Vaduz',
    'Europe/Vatican','Europe/Vienna','Europe/Vilnius','Europe/Volgograd',
    'Europe/Warsaw','Europe/Zagreb','Europe/Zaporozhye','Europe/Zurich',
    // 印度
    'Indian/Antananarivo','Indian/Chagos','Indian/Christmas','Indian/Cocos',
    'Indian/Comoro','Indian/Kerguelen','Indian/Mahe,Indian/Maldives',
    'Indian/Mauritius','Indian/Mayotte','Indian/Reunion',
    // 太平洋
    'Pacific/Apia','Pacific/Auckland','Pacific/Bougainville','Pacific/Chatham',
    'Pacific/Chuuk','Pacific/Easter','Pacific/Efate','Pacific/Enderbury',
    'Pacific/Fakaofo','Pacific/Fiji','Pacific/Funafuti','Pacific/Galapagos',
    'Pacific/Gambier','Pacific/Guadalcanal','Pacific/Guam','Pacific/Honolulu',
    'Pacific/Kiritimati','Pacific/Kosrae','Pacific/Kwajalein','Pacific/Majuro',
    'Pacific/Marquesas','Pacific/Midway','Pacific/Nauru','Pacific/Niue',
    'Pacific/Norfolk','Pacific/Noumea','Pacific/Pago_Pago','Pacific/Palau',
    'Pacific/Pitcairn','Pacific/Pohnpei','Pacific/Port_Moresby','Pacific/Rarotonga',
    'Pacific/Saipan','Pacific/Tahiti','Pacific/Tarawa','Pacific/Tongatapu',
    'Pacific/Wake','Pacific/Wallis',
    // 其他
    'Africa/Asmera','Africa/Timbuktu','America/Argentina/ComodRivadavia','America/Atka',
    'America/Buenos_Aires','America/Catamarca','America/Coral_Harbour','America/Cordoba',
    'America/Ensenada','America/Fort_Wayne','America/Indianapolis','America/Jujuy',
    'America/Knox_IN','America/Louisville','America/Mendoza','America/Montreal',
    'America/Porto_Acre','America/Rosario','America/Santa_Isabel','America/Shiprock',
    'America/Virgin','Antarctica/South_Pole','Asia/Ashkhabad','Asia/Calcutta',
    'Asia/Chongqing','Asia/Chungking','Asia/Dacca','Asia/Harbin','Canada/Mountain',
    'Asia/Istanbul','Asia/Kashgar','Asia/Katmandu','Asia/Macao','Canada/Newfoundland',
    'Asia/Rangoon','Asia/Saigon','Asia/Tel_Aviv','Asia/Thimbu','Canada/Yukon',
    'Asia/Ujung_Pandang','Asia/Ulan_Bator','Atlantic/Faeroe','Atlantic/Jan_Mayen',
    'Canada/Central','Canada/Eastern','Egypt','Canada/Pacific','Canada/Saskatchewan',
    'Chile/Continental','Chile/EasterIsland','CST6CDT','Cuba','EET','Eire','EST',
    'EST5EDT','Etc/GMT','Etc/GMT+0','Etc/GMT+1','Etc/GMT+10','Etc/GMT+11','CET',
    'Etc/GMT+12','Etc/GMT+2','Etc/GMT+3','Etc/GMT+4','Etc/GMT+5','Etc/GMT+6',
    'Etc/GMT+7','Etc/GMT+8','Etc/GMT+9','Etc/GMT-0','Etc/GMT-1','Etc/GMT-10',
    'Etc/GMT-11','Etc/GMT-12','Etc/GMT-13','Etc/GMT-14','Etc/GMT-2','Etc/GMT-3',
    'Etc/GMT-4','Etc/GMT-5','Etc/GMT-6','Etc/GMT-7','Etc/GMT-8','Etc/GMT-9',
    'Etc/GMT0','Etc/Greenwich','Etc/UCT','Etc/Universal','Etc/UTC','Etc/Zulu',
    'Europe/Belfast','Europe/Nicosia','Europe/Tiraspol','Factory','GB','GB-Eire',
    'GMT','GMT+0','GMT-0','GMT0','Greenwich','Hongkong','HST','Iceland','Iran',
    'Israel','Jamaica','Japan','Kwajalein','Libya','MET','Mexico/BajaNorte',
    'Mexico/BajaSur','Mexico/General','MST','MST7MDT','Navajo','NZ','Singapore',
    'NZ-CHAT','Pacific/Johnston','Pacific/Ponape','Pacific/Samoa','ROC','ROK',
    'Pacific/Truk','Pacific/Yap','Poland','Portugal','Turkey','UCT','Universal',
    'US/Alaska','US/Aleutian','US/Arizona','US/Central','PRC','PST8PDT',
    'US/East-Indiana','US/Eastern','US/Hawaii','US/Indiana-Starke',
    'US/Michigan','US/Mountain','US/Pacific','US/Pacific-New',
    'US/Samoa','UTC	W-SU','WET','Zulu'	
]; //下拉框选项
    $scope.datatype = [
      { name: '手动配置', value: 'hand' },
      { name: '自动同步本地系统时间', value: 'local' },
      { name: 'NTP服务器时间同步', value: 'ntp' },
    ];

    $scope.selectedName = 'hand';
    // $scope.zoneSelect = 'UTC+8';
    $scope.zonedisabled = false;
    $scope.timedisabled = false;
    $scope.ntpdisabled = true;
    $scope.zoneData = [
      { name: 'UTC +14(莱恩群岛)', value: 'UTC+14' },
      { name: 'UTC +13:45(查塔姆岛夏令)', value: 'UTC+13:45' },
      { name: 'UTC +13(新西兰夏令,汤加,斐济夏令,凤凰岛)', value: 'UTC+13' },
      { name: 'UTC +12:45(查塔姆岛标准)', value: 'UTC+12:45' },
      { name: 'UTC +12(新西兰标准,瑙鲁,斐济,马加丹夏令)', value: 'UTC+12' },
      { name: 'UTC +11(澳大利亚东部夏令,所罗门群岛)', value: 'UTC+11' },
      { name: 'UTC +10:30(澳大利亚中部夏令,豪勋爵)', value: '+10:30' },
      { name: 'UTC +10(澳大利亚东部标准,巴布亚新几内亚)', value: 'UTC+10' },
      { name: 'UTC +9:30(澳大利亚中央标准)', value: 'UTC+9:30' },
      { name: 'UTC +9(日本,韩国,印尼东部,东帝汶)', value: 'UTC+9' },
      { name: 'UTC +8:45(澳大利亚中部西部标准)', value: 'UTC+8:45' },
      { name: 'UTC +8:30(平壤)', value: 'UTC+8:30' },
      { name: 'UTC +8(中国标准,香港,新加坡,马来西亚,菲律宾)', value: 'UTC+8' },
      { name: 'UTC +7(印尼西部,印度支那,圣诞岛,戴维斯)', value: 'UTC+7' },
      { name: 'UTC +6:30(缅甸,科科斯群岛)', value: 'UTC+6:30' },
      {
        name: 'UTC +6(孟加拉国,吉尔吉斯斯坦,东方号站,新西伯利亚)',
        value: 'UTC+6',
      },
      { name: 'UTC +5:30(印度标准)', value: 'UTC+5:30' },
      {
        name: 'UTC +5(巴基斯坦标准,土库曼斯坦,乌兹别克斯坦,马尔代夫)',
        value: 'UTC+5',
      },
      { name: 'UTC +4:30(伊朗夏令,阿富汗)', value: 'UTC+4:30' },
      {
        name: 'UTC +4(莫斯科,阿塞拜疆,海湾,乔治亚州,亚美尼亚)',
        value: 'UTC+4',
      },
      { name: 'UTC +3:30(伊朗标准)', value: 'UTC+3:30' },
      {
        name: 'UTC +3(欧洲东部夏令,非洲东部,莫斯科标准,阿拉伯)',
        value: 'UTC+3',
      },
      { name: 'UTC +2(欧洲东部,非洲中部,南非标准,以色列标准)', value: 'UTC+3' },
      { name: 'UTC +1(欧洲中部时间,爱尔兰标准,英国夏令)', value: 'UTC+1' },
      { name: 'UTC +0(格林威治标准,西欧,西撒哈拉标准)', value: 'UTC+0' },
      { name: 'UTC -1(格陵兰岛东部,亚速尔群岛)', value: 'UTC-1' },
      { name: 'UTC -2(巴西利亚夏令,南乔治亚岛)', value: 'UTC-2' },
      { name: 'UTC -2:30(纽芬兰夏令)', value: 'UTC-2:30' },
      { name: 'UTC -3(巴西利亚,乌拉圭,阿根廷,苏里南)', value: 'UTC-3' },
      { name: 'UTC -3:30(纽芬兰标准)', value: 'UTC-3:30' },
      { name: 'UTC -4(大西洋标准,委内瑞拉,巴拉圭,亚马逊)', value: 'UTC-4' },
      { name: 'UTC -5(北美东部,哥伦比亚,厄瓜多尔,秘鲁)', value: 'UTC-5' },
      { name: 'UTC -6(北美中部,复活节岛)', value: 'UTC-6' },
      { name: 'UTC -7(北美山地标准,太平洋夏令)', value: 'UTC-7' },
      { name: 'UTC -8(太平洋标准,皮特克恩标准)', value: 'UTC-8' },
      { name: 'UTC -9(阿拉斯加标准,夏威夷阿留申夏令)', value: 'UTC-9' },
      { name: 'UTC -10(夏威夷阿留申,库克岛,塔希提岛)', value: 'UTC-10' },
      { name: 'UTC -11(萨摩亚标准,纽埃岛)', value: 'UTC-11' },
    ];
    // $scope.zone_box();  //时区插件外壳
    $scope.get_server_time();//   获取服务器时间
  };
  //时区插件外壳
  $scope.zone_box = function (params) {
    $('#map').timezonePicker({
        quickLink: [{
            "中央标准时间":"CST",
            "太平洋标准时间":"PST",
            "格林威治时间":"GMT"
            }]
    });
  }
//   时区选择插件 设置
  $scope.time_zone = function (params) {
        $('#map').data('timezonePicker').setValue(params);
        // console.log($('#map').data('timezonePicker').getValue());
  }

//   获取服务器时间
  $scope.get_server_time = function (params) {
    $http.get('/seting/time-synchronization').then(function success(rsp) {
        // console.log(rsp.data);
        if(rsp.data.time == null){
            $scope.timerChoose = {
                timePickerIncrement: 10,
                startDate: moment()
              };
        }else{
            $scope.timerChoose = {
                timePickerIncrement: 10,
                startDate: rsp.data.time
          };
        }
        $scope.datapicker($scope.timerChoose);
        $scope.ntpTime = rsp.data.server
    }, function err(rsp) {
        console.log(rsp);
    });
      
  }

  $scope.typeChange = function(params) {
    // console.log($scope.selectedName);
    if ($scope.selectedName == 'hand') {
      $scope.zonedisabled = false;
      $scope.timedisabled = false;
      $scope.ntpdisabled = true;
    //   获取服务器时间
    $scope.get_server_time();
    } else if ($scope.selectedName == 'local') {
      $scope.zonedisabled = false;
      $scope.timedisabled = true;
      $scope.ntpdisabled = true;
    //   自动同步本地时间
       $scope.timerChoose = {
        timePickerIncrement: 10,
        startDate: moment()
      };
      $scope.datapicker($scope.timerChoose);
    } else if ($scope.selectedName == 'ntp') {
      $scope.zonedisabled = true;
      $scope.timedisabled = true;
      $scope.ntpdisabled = false;
    }
  };

  // 时间插件
  $scope.datapicker = function(choosetime) {
    // console.log(moment().subtract(7, 'days'));
    $('#reservationtime').daterangepicker(
      {
        startDate: choosetime.startDate,
        singleDatePicker: true,
        // showDropdowns: true, //当设置值为true的时候，允许年份和月份通过下拉框的形式选择
        // autoApply: false, // true不用点击Apply或者应用按钮就可以直接取得选中的日期
        timePicker: true,
        timePicker24Hour: true,
        timePickerSeconds: true,
        locale: {
          applyLabel: '确定',
          cancelLabel: '取消',
          format: 'YYYY-MM-DD HH:mm:ss',
        },
      },
      function(start, end, label) {
        // console.log('111');

        $scope.startTime = $filter('date')(
          start.unix() * 1000,
          'yyyy-MM-dd HH:mm:ss'
        );
        // $scope.endTime = $filter('date')(end.unix() * 1000, 'yyyy-MM-dd');
        $scope.timerChoose.startDate = $scope.startTime;
        // console.log($scope.startTime);
      }
    );
  };

  //   保存
  $scope.save = function(params) {
    //手动配置
    if($scope.selectedName =='hand'){
        $scope.loading =  zeroModal.loading(4)
        var rqs_data = {
            time:$scope.timerChoose.startDate,
            zone:$scope.zoneresult
        };
        $http.put("/seting/manual-time-synchronization",rqs_data).then(function success(rsp){
            zeroModal.close( $scope.loading);
            if(rsp.data.result == 'ok'){
                zeroModal.success('保存成功');
            }
        },function err(rsp){
              console.log(rsp);
            zeroModal.close($scope.loading);
            zeroModal.error('保存失败!');
        });
    }
    //本地自动同步
    if($scope.selectedName =='local'){
        $scope.loading =  zeroModal.loading(4)
        var rqs_data = {
            time:moment(),
            zone:$scope.zoneresult
        };
        $http.put("/seting/manual-time-synchronization",rqs_data).then(function success(rsp){
            // console.log(rsp);
            zeroModal.close( $scope.loading);
            if(rsp.data.result == 'ok'){
                zeroModal.success('保存成功')
            }
        },function err(rsp){
              console.log(rsp);
            zeroModal.close($scope.loading);
            zeroModal.error('保存失败!');
        });

    }
    // ntp服务同步
    if($scope.selectedName =='ntp'){
        $scope.loading =  zeroModal.loading(4)
        var rqs_data = {
            server:$scope.ntpTime
        };
        $http.put("/seting/ntp-time-synchronization",rqs_data).then(function success(rsp){
            // console.log(rsp);
            zeroModal.close( $scope.loading);
            if(rsp.data.result == 'ok'){
                zeroModal.success('保存成功')
            }
        },function err(rsp){
            //   console.log(rsp);
            zeroModal.close($scope.loading);
            zeroModal.error('保存失败!');
        });

    };
    // console.log($scope.selectedName); //配置方式
    // console.log($scope.timerChoose.startDate); // 日期选择
    // console.log($scope.ntpTime); // ntp时间
    // console.log( $scope.zoneresult );
  };
  $scope.init();
});

myApp.directive('selectSearch', function($compile,$http) {
    return {
    restrict: 'AE', //attribute or element
    scope: {
     datas: '=',
     //bindAttr: '='
     zonedisabled:'=',
     searchvalue:'='
    },
    template:
    '<input type = "test"    ng-disabled="zonedisabled" class="input_change" ng-change="changeKeyValue(searchField)" ng-model="searchField" style = "display:block;" '+
    'ng-click = "hidden=!hidden" ng-focus="focusinput()" value="{{searchField}}"/></input>'+
    '<div ng-hide="hidden" class="slect_content">'+
    ' <select style = "width:190px;" ng-change="change(x)" ng-model="x" multiple>'+
    '  <option ng-click="chooseitem(data)" ng-repeat="data in datas" >{{data}}</option>'+
    ' </select>'+
    '</div>',
    // replace: true,
    link: function($scope, elem, attr, ctrl) {
        $scope.loading =  zeroModal.loading(4);
        $http.get('/seting/time-synchronization').then(function success(rsp) {
            // console.log(rsp.data);
            $scope.serverzonetime = rsp.data.zone;
            $scope.tempdatas = $scope.datas; //下拉框选项副本
            $scope.hidden=true;//选择框是否隐藏
            $scope.searchField='';//文本框数据
            zeroModal.close($scope.loading);
           //   服务器获取的时区
           if($scope.serverzonetime){
               $scope.searchField = $scope.serverzonetime;
               $scope.searchvalue = $scope.serverzonetime;
           }
           //将下拉选的数据值赋值给文本框
            $scope.change=function(x){
            //    console.log(x);
             $scope.searchField=x;
             $scope.searchvalue = x[0];
             $scope.hidden=true; 
            }
            $scope.focusinput = function (params) {
                // console.log( );
                
            }
            $scope.chooseitem = function (params) {
                $scope.searchField = params;
                $scope.searchvalue = params;
                $scope.hidden=true; 
            }
           //获取的数据值与下拉选逐个比较，如果包含则放在临时变量副本，并用临时变量副本替换下拉选原先的数值，如果数据为空或找不到，就用初始下拉选项副本替换
            $scope.changeKeyValue=function(v){
            $scope.datas=$scope.tempdatas;
             $scope.newDate=[]; //临时下拉选副本
           
            //如果包含就添加
                v = angular.lowercase(v)
             angular.forEach($scope.datas ,function(data,index,array){
              if(angular.lowercase(data).indexOf(v)>=0){
                $scope.newDate.unshift(data);
              }
             });
            //用下拉选副本替换原来的数据
             $scope.datas=$scope.newDate;
            //下拉选展示
             $scope.hidden=false;
            //如果不包含或者输入的是空字符串则用初始变量副本做替换
             if($scope.datas.length==0 || ''==v){
              $scope.datas=$scope.tempdatas;
             }
            }
        }, function err(rsp) {
            console.log(rsp);
        });
    }
    };
   });

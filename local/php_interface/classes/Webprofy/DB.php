<?

	namespace Webprofy;

	class DB{
		private static
			$databases = array(
				'example' => array(
					'string' => 'mysql:unix_socket=/var/lib/mysqld/mysqld.sock;host=localhost;dbname=old_ultrabike;charset=utf8',
					'login' => 'root',
					'password' => ''
				)
			),
			$dbSettings = null,
			$dbReset = false,
			$pdo = null;

		static function setDatabase($type = false){
			self::$dbReset = true;

			if(isset(self::$databases[$type])){
				self::$dbSettings = self::$databases[$type];
				return;
			}

			include($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/dbconn.php');
			self::$dbSettings = array(
				'string' => $DBType.':host='.$DBHost.';dbname='.$DBName.';charset=utf8',
				'login' => $DBLogin,
				'password' => $DBPassword
			);
		}

		static function lastID(){
			if(!self::$pdo){
				return 0;
			}

			return self::$pdo->lastInsertId();
		}

		static function query(
			$query,
			$data = false,
			$callback = false
		){
			$pdo = self::$pdo;
			if(!$pdo || self::$dbReset){
				try{
					if(self::$dbSettings == null){
						self::setDatabase();
					}
					self::$pdo = $pdo = new \PDO(
						self::$dbSettings['string'],
						self::$dbSettings['login'],
						self::$dbSettings['password']
					);
					self::$dbReset = false;
				}
				catch(PDOException $e){}
			}


			try{
				$result = $pdo->prepare($query);
				if($data){
					if(!is_array($data)){
						$data = array($data);
					}
					$result->execute($data);
				}
				else{
					$result->execute();
				}
				if(is_callable($callback)){
					while(($row = $result->fetch(PDO::FETCH_ASSOC)) !== FALSE){
						$result = $callback($row);
						if($result === false){
							break;
						}
					}
					return true;
				}
				$all = $result->fetchAll(PDO::FETCH_ASSOC);
			}
			catch(PDOException $e){
				$all = array();
			}

			return $all;
		}
	}
?>
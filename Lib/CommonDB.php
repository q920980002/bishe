<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/22
 * Time: 下午1:24
 */
namespace Robotx\Lib;
class CommonDB{

    /**@var \PDO  */
    private $_pdo;

    /**@var string*/
    private $_auth = 'RW';

    /**@var integer*/
    private $_risk = 100;

    /**@var boolean*/
    private $_autoTrans = true;

    /**
     * @param array $config
     */
    public function __construct($config = array())
    {
        if (empty($config)) {
            return false;
        }

        $dns = sprintf('%s:host=%s;port=%d;dbname=%s', $config['provider'], $config['host'], $config['port'], $config['dbname']);
        $pdo = new \PDO($dns, $config['user'], $config['passwd'],[\PDO::ATTR_PERSISTENT=>true]);
        if (!$pdo) {
            return false;
        }

        $this->_risk = isset($config['risk']) ? $config['risk'] : 100;
        $this->_auth = strtoupper($config['auth']);
        $this->_pdo = $pdo;
        $this->_pdo->query('SET NAMES "UTF8"');
        $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);            //错误以异常来显示
        $this->_pdo->setAttribute(\PDO::ATTR_CASE,\PDO::CASE_NATURAL);                    //列名按照原始的方式

        return $this;

    }

    /**
     * @param $sql
     * @return bool|\PDOStatement
     */
    public function sql($sql)
    {
        if (!$sql) {
            return false;
        }
        return $this->_pdo->query($sql, \PDO::FETCH_ASSOC);
    }

    /**
     * @param $sql
     * @return array|bool
     */
    public function select($sql)
    {
        if (!$sql) {
            return false;
        }
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 返回查询条数
     * @param $sql
     * @return string
     */
    public function count($sql)
    {
        if (!$sql) {
            return false;
        }
        $rs = $this->_pdo->query($sql);
        return $rs->fetchColumn();
    }

    /**
     * @param $sql
     * @return bool|int
     * @throws \Exception
     */
    public function update($sql)
    {
        if ($this->_auth !== 'RW') {
            throw new \Exception('当前连接为只读库，不允许更新数据');
        }
        //事务模式
        if ($this->_autoTrans) {
            return $this->_transaction($sql);
        } else {
            return $this->_pdo->exec($sql);
        }
    }

    /**
     * @param $sql
     * @return bool|int|string
     * @throws \Exception
     */
    public function insert($sql){
        if ($this->_auth !== 'RW') {
            throw new \Exception('当前连接为只读库，不允许更新数据');
        }
        //事务模式
        if ($this->_autoTrans) {
            $last_id = $this->_transaction($sql);
        } else {
            $this->_pdo->exec($sql);
            $last_id = $this->_pdo->lastInsertId();
        }
        return $last_id;
    }

    /**
     * 手动开启事务
     */
    public function beginTrans()
    {
        $this->_autoTrans = false;
        $this->_pdo->beginTransaction();
    }

    /**
     * 手动提交事务
     */
    public function commit()
    {
        $this->_pdo->commit();
        $this->_autoTrans = true;
    }

    /**
     * 手动回滚事务
     */
    public function rollBack()
    {
        $this->_pdo->rollBack();
        $this->_autoTrans = true;
    }

    /**
     * @param $sql
     * @return bool|int
     * @throws \Exception
     */
    private function _transaction($sql)
    {
        $this->_pdo->beginTransaction();
        try {
            $ret = $this->_pdo->exec($sql);
            //影响行数大于规定行数,回滚----害怕误删
            if ($ret > $this->_risk) {
                $this->_pdo->rollBack();
                return false;
            } else {
                if (stristr($sql, 'insert') !== false) {
                    $ret = $this->_pdo->lastInsertId();
                }
                $this->_pdo->commit();
                return $ret;
            }
        } catch (\Exception $e) {
            $this->_pdo->rollBack();
            throw new \Exception(sprintf('事务执行异常，错误信息:[%s]，当前sql:[%s]', $e->getMessage(), $sql));
        }
    }



}
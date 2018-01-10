<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2017/12/18
 * Time: 18:54
 */
namespace core;

class Model
{
    /**
     * @var null
     * 保存连接信息
     */
    public static $link = NULL;
    /**
     * @var null
     * 保存表名
     */
    protected $table = NULL;
    /**
     * @var
     * 初始化表信息
     * 此变量是链式调用方法的关键
     */
    private $opt;

    /**
     * @var array
     * 记录发送的sql
     */
    public static $sqls = array();

    /**
     * Model constructor.
     * Model构造方法
     * @param null $table
     */
    public function __construct($table = NULL)
    {
        $this->table = is_null($table) ? C('DB_PREFIX') . $this->table : C('DB_PREFIX') . $table;
        //连接数据库
        $this->_connect();
        //初始化sql信息
        $this->_opt();

    }

    /**
     * [_connect]
     * 连接数据库
     */
    private function _connect()
    {
        if (is_null(self::$link)) {
            if (empty(C('DB_DATABASE'))) halt('请先配置数据库');
            $link = new \Mysqli(C('DB_HOST'), C('DB_USER'), C('DB_PASSWORD'), C('DB_DATABASE'), C('DB_PORT'));
            if ($link->connect_error) halt('数据库连接错误，请检查配置项');
            $link->set_charset(C('DB_CHARSET'));
            self::$link = $link;
        }
    }

    /**
     * [query]
     * 底层查询方法（有结果集）
     * @param $sql
     * @return array
     */
    public function query($sql)
    {
        self::$sqls[] = $sql;
        $link = self::$link;
        $result = $link->query($sql);
        if ($link->errno) halt('mysql错误：' . $link->error . '<br/>SQL:' . $sql);
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
        $this->_opt();
        return $rows;
    }

    /**
     * [one]
     * find()函数的别名函数
     * @return array|mixed
     */
    public function one()
    {
        return $this->find();
    }

    /**
     * [find]
     * 只查找一条数据
     * @return array|mixed
     */
    public function find()
    {
        $data = $this->limit(1)->all();
        $data = current($data);//返回当前单元
        return $data;
    }

    /**
     * [having]
     * sql查询having设置
     * @param $having
     * @return $this
     */
    public function having($having)
    {
        $this->opt['having'] = " HAVING " . $having;
        return $this;//return $thhis是链式调用方法的关键
    }

    /**
     * [group]
     * sql查询group设置
     * @param $group
     * @return $this
     */
    public function group($group)
    {
        $this->opt['group'] = " GROUP BY " . $group;
        return $this;//return $thhis是链式调用方法的关键
    }

    /**
     * [limit]
     * sql查询limit设置
     * @param $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->opt['limit'] = " LIMIT " . $limit;
        return $this;//return $thhis是链式调用方法的关键
    }

    /**
     * [order]
     * sql查询order设置
     * @param $order
     * @return $this
     */
    public function order($order)
    {
        $this->opt['order'] = " ORDER BY " . $order;
        return $this;//return $thhis是链式调用方法的关键
    }

    /**
     * [where]
     * sql查询where设置
     * @param $where
     * @return $this
     */
    public function where($where)
    {
        $this->opt['where'] = " WHERE" . $where;
        return $this;//return $thhis是链式调用方法的关键
    }

    /**
     * [field]
     * sql查询字段设置
     * @param $field
     * @return $this
     */
    public function field($field)
    {
        $this->opt['field'] = $field;
        return $this;//return $this是链式调用方法的关键
    }

    /**
     * [findAll]
     * all()的别名函数
     * @return array
     */
    public function findAll()
    {
        return $this->all();
    }

    /**
     * [all]
     * 查询所有数据
     * 组合链式调试的关键，如：M('user')->field('name')->limit(1)->all();
     * @return array
     */
    public function all()
    {
        $sql = "SELECT " . $this->opt['field'] . " FROM " . $this->table . $this->opt['where'] . $this->opt['group'] . $this->opt['having'] . $this->opt['order'] . $this->opt['limit'];
        return $this->query($sql);
    }

    /**
     * [_opt]
     * 初始化表信息
     */
    private function _opt()
    {
        $this->opt = array(
            'field' =>'*',
            'where' =>'',
            'group' =>'',
            'having' =>'',
            'order' =>'',
            'limit' =>''
        );
    }

    /**
     * [exe]
     * 没结果集方法
     * @param $sql
     * @return mixed
     */
    public function exe($sql)
    {
        self::$sqls[] = $sql;
        $link = self::$link;
        $bool = $link->query($sql);
        $this->_opt();
        if(is_object($bool)){
            //如果用户操作错误，执行有返回结果集的语句
            halt('请用query方法发送查询sql');
        }

        if($bool){
            return $link->insert_id ? $link->insert_id : $link->affected_rows;//有自增id则返回自增id，没则返回受影响行数
        }else{
            halt('mysql错误：' . $link->error . '<br/>SQL: ' . $sql);
        }
    }

    /**
     * [delete]
     * 删除方法
     * @return mixed
     */
    public function delete()
    {
        if(empty($this->opt['where'])) halt('删除语句必须有where条件！');
        $sql = "DELETE FROM " . $this->table . $this->opt['where'];
        return $this->exe($sql);
    }

    /**
     * [_safe_str]
     * 设置安全转义字符串
     * @param $str
     * @return mixed
     */
    private function _safe_str($str)
    {
        //如果开启了系统自动转义，则把它转回来
        if(get_magic_quotes_gpc()){
            $str = stripcslashes($str);
        }
        //要用mysqli的自动转义
        return self::$link->real_escape_string($str);
    }

    /**
     * [add]
     * 添加方法
     * @param null $data
     * @return mixed
     */
    public function add($data=NULL)
    {
        if(is_null($data)) $data = $_POST;
        $fields = '';
        $values = '';

        foreach ($data as $f => $v)
        {
            $fields .= "`" .$this->_safe_str($f) . "`,";
            $values .= "'" .$this->_safe_str($v) . "',";
        }

        $fields = trim($fields,',');
        $values = trim($values,',');

        $sql = "INSERT INTO " . $this->table . '(' . $fields . ') VALUES (' . $values . ')';
        return $this->exe($sql);
    }

    /**
     * [update]
     * 更新方法
     * @param null $data
     * @return mixed
     */
    public function update($data=NULL)
    {
        if(empty($this->opt['where'])) halt('更新语句必须有where条件！');
        if(is_null($data)) $data = $_POST;
        $values = '';
        foreach ($data as $f => $v)
        {
            $values .= "`" . $this->_safe_str($f) . "`='" . $this->_safe_str($v) . "',";
        }
        $values = trim($values,',');
        $sql = "UPDATE " . $this->table . " SET " . $values . $this->opt['where'];
        return $this->exe($sql);
    }
}
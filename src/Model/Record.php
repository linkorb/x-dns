<?php

namespace XDns\Model;

use RuntimeException;

class Record
{
    protected $name;
    protected $type;
    protected $ttl;
    protected $value;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getTtl()
    {
        return $this->ttl;
    }

    public function setTtl($ttl)
    {
        if (!is_numeric($ttl)) {
            throw new RuntimeException("Non numeric ttl: " . $ttl);
        }
        $this->ttl = $ttl;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $type= strtoupper($type);
        switch ($type) {
            case 'A':
            case 'AAAA':
            case 'CNAME':
            case 'MX':
            case 'NS':
            case 'TXT':
            case 'SRV':
            case 'SSHFP':
            case 'TLSA':
            case 'CAA':
                break;
            default:
                throw new RuntimeException("Unsupported record type: " . $type);
        }
        $this->type = $type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getKey()
    {
        return $this->getType() . ':' . $this->getName();
    }

    public function getString(): string
    {
        $str = $this->name . "\t";
        $str .= $this->type . "\t";
        $str .= $this->value . "\t";
        $str .= $this->ttl;
        return $str;
    }


}

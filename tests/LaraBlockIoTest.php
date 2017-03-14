<?php

class LaraBlockIoTest extends Orchestra\Testbench\TestCase
{
    
    public $dTrustLabel;
    public $s1;
    public $s2;
    public $s3;
    
    public function setUp()
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return ['Blockavel\LaraBlockIo\LaraBlockIoServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return [
            'LaraBlockIo' => 'Blockavel\LaraBlockIo\LaraBlockIoFacade'
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('larablockio.apiKey', getenv('BLOCKIO_API_KEY'));
        $app['config']->set('larablockio.pin', getenv('BLOCKIO_PIN'));
        $app['config']->set('larablockio.version', getenv('BLOCKIO_VERSION'));
    }

    public function randomString($length = 10) {
    	$str = "";
    	$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
    	$max = count($characters) - 1;
    	for ($i = 0; $i < $length; $i++) {
    		$rand = mt_rand(0, $max);
    		$str .= $characters[$rand];
    	}
    	return $str;
    }
    
    public function setProperties()
    {
        $this->dTrustLabel = $this->randomString();
        $this->s1 = $this->randomString();
        $this->s2 = $this->randomString();
        $this->s3 = $this->randomString();
    }
    
    public function getDTrustLabel()
    {
        return $this->dTrustLabel;
    }
    
    public function getS1()
    {
        return $this->s1;
    }
    
    public function getS2()
    {
        return $this->s2;
    }
    
    public function getS3()
    {
        return $this->s3;
    }
    public function testGetBalanceInfo()
    {

        $res = LaraBlockIo::getBalanceInfo();
        $this->assertTrue(gettype($res) == 'object');
        $this->assertArrayHasKey('status', (array) $res);
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('available_balance', (array) $res->data);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data);

    }

    public function testGetNetwork()
    {

        $res = LaraBlockIo::getNetwork();

        $this->assertTrue($res == 'BTCTEST' || $res = "LTCTEST" || $res = "DGCTEST");
    }

    public function testGetAvailableBalance()
    {

        $res = LaraBlockIo::getAvailableBalance();

        $this->assertTrue(is_numeric($res) && $res >= 0);
    }

    public function testGetPendingReceivedBalance()
    {

        $res = LaraBlockIo::getPendingReceivedBalance();

        $this->assertTrue(is_numeric($res) && $res >= 0);
    }

    public function testCreateAddress()
    {

        $label = $this->randomString();

        $res = LaraBlockIo::createAddress($label);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::createAddress($label);

        $this->assertTrue(gettype($res) == 'object');
        $this->assertArrayHasKey('status', (array) $res);
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('user_id', (array) $res->data);
        $this->assertArrayHasKey('address', (array) $res->data);
        $this->assertArrayHasKey('label', (array) $res->data);
        
        LaraBlockIo::archiveAddressesByLabels($label);
        
    }

    public function testGetAddressInfo()
    {

        $res = LaraBlockIo::getAddressesInfo();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('available_balance', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data->addresses[0]);
    }

    public function testGetAddressesInfoWithoutBalances()
    {

        $res = LaraBlockIo::getAddressesInfoWithoutBalances();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
    }

    public function testGetAddresses()
    {

        $res = LaraBlockIo::getAddresses();

        $this->assertArrayHasKey('user_id', (array) $res[0]);
        $this->assertArrayHasKey('address', (array) $res[0]);
        $this->assertArrayHasKey('label', (array) $res[0]);
        $this->assertArrayHasKey('available_balance', (array) $res[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res[0]);
    }

    public function testGetAddressesWithoutBalance()
    {

        $res = LaraBlockIo::getAddresses();

        $this->assertArrayHasKey('user_id', (array) $res[0]);
        $this->assertArrayHasKey('address', (array) $res[0]);
        $this->assertArrayHasKey('label', (array) $res[0]);
    }

    public function testGetAddressesBalanceByAddress()
    {

        $addresses = LaraBlockIo::getAddresses()[0]->address;

        $res = LaraBlockIo::getAddressesBalanceByAddress($addresses);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('balances', (array) $res->data);
        $this->assertArrayHasKey('user_id', (array) $res->data->balances[0]);
        $this->assertArrayHasKey('address', (array) $res->data->balances[0]);
        $this->assertArrayHasKey('label', (array) $res->data->balances[0]);
        $this->assertArrayHasKey('available_balance', (array) $res->data->balances[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data->balances[0]);

        $addresses = $this->randomString();

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getAddressesBalanceByAddress($addresses);

    }

    public function testGetAddressesBalanceByLabels()
    {

        $labels = LaraBlockIo::getAddresses()[0]->label;

        $res = LaraBlockIo::getAddressesBalanceByLabels($labels);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('balances', (array) $res->data);
        $this->assertArrayHasKey('user_id', (array) $res->data->balances[0]);
        $this->assertArrayHasKey('address', (array) $res->data->balances[0]);
        $this->assertArrayHasKey('label', (array) $res->data->balances[0]);
        $this->assertArrayHasKey('available_balance', (array) $res->data->balances[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data->balances[0]);

        $labels = $this->randomString();

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getAddressesBalanceByLabels($labels);
    }

    public function testGetAddressByLabel()
    {

        $label = LaraBlockIo::getAddresses()[0]->label;

        $res = LaraBlockIo::getAddressByLabel($label);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('address', (array) $res->data);
        $this->assertArrayHasKey('user_id', (array) $res->data);
        $this->assertArrayHasKey('address', (array) $res->data);
        $this->assertArrayHasKey('label', (array) $res->data);
        $this->assertArrayHasKey('available_balance', (array) $res->data);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data);

        $label = $this->randomString();

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getAddressByLabel($label);
    }

    public function testGetUsers()
    {

        $res = LaraBlockIo::getUsers();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('user_id', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('address', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('label', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('available_balance', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data->addresses[0]);
    }

    public function testGetUsersBalance()
    {

        $users = LaraBlockIo::getUsers()->data->addresses[0]->user_id;

        $res = LaraBlockIo::getUsersBalance($users);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('available_balance', (array) $res->data);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data);
        $this->assertArrayHasKey('balances', (array) $res->data);
        $this->assertArrayHasKey('user_id', (array) $res->data->balances[0]);
        $this->assertArrayHasKey('label', (array) $res->data->balances[0]);
        $this->assertArrayHasKey('address', (array) $res->data->balances[0]);
        $this->assertArrayHasKey('available_balance', (array) $res->data->balances[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data->balances[0]);

        $users = $this->randomString();

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getUsersBalance($users);
    }

    public function testGetUserAddress()
    {

        $user = LaraBlockIo::getUsers()->data->addresses[0]->user_id;

        $res = LaraBlockIo::getUserAddress($user);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('user_id', (array) $res->data);
        $this->assertArrayHasKey('address', (array) $res->data);
        $this->assertArrayHasKey('label', (array) $res->data);
        $this->assertArrayHasKey('confirmed_balance', (array) $res->data);
        $this->assertArrayHasKey('unconfirmed_balance', (array) $res->data);

        $user = $this->randomString();

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getUserAddress($user);
    }
    
    public function cmp($a, $b)
    {
        return $a->available_balance < $b->available_balance;
    }
    
    public function testGetNetworkFeeEstimate()
    {

        $addresses = LaraBlockIo::getAddresses();

        usort($addresses, array($this, "cmp"));
        
        $amount = $addresses[0]->available_balance * 0.5;

        if($amount > .001)
        {
            $address = $addresses[count($addresses) - 1];

            $res = LaraBlockIo::getNetworkFeeEstimate($amount, $address->address);

            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('estimated_network_fee', (array) $res->data);
        }

        $this->expectException(Exception::class);

        $address = $this->randomString();

        $res = LaraBlockIo::getNetworkFeeEstimate($amount, $address);
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $address = $addresses[count($addresses)]->address;
        
        $amount = 0;

        $res = LaraBlockIo::getNetworkFeeEstimate($amount, $address);

    }
    
    public function testWithdraw()
    {
        $addresses = LaraBlockIo::getAddresses();

        usort($addresses, array($this, "cmp"));
        
        if($addresses[0]->available_balance * 0.5 > 0.001)
        {
            $amount = .001;
            $toAddresses = $addresses[count($addresses) - 1]->address;
            $res = LaraBlockIo::withdraw($amount, $toAddresses);
            
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('txid', (array) $res->data);
            $this->assertArrayHasKey('amount_withdrawn', (array) $res->data);
            $this->assertArrayHasKey('amount_sent', (array) $res->data);
            $this->assertArrayHasKey('network_fee', (array) $res->data);
        }
        
        $this->expectException(Exception::class);
        
        $toAddresses = $this->randomString();
        
        $res = LaraBlockIo::withdraw($amount, $toAddresses);
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $toAddresses = $addresses[count($addresses)]->address;
        
        $amount = 0;

        $res = LaraBlockIo::withdraw($amount, $toAddresses);
    }
    
    public function testWithdrawFromAddressesToAddresses()
    {
        $addresses = LaraBlockIo::getAddresses();

        usort($addresses, array($this, "cmp"));
        
        if($addresses[0]->available_balance * 0.5 > 0.001)
        {
            $amounts = .001;
            $fromAddresses = $addresses[0]->address;
            $toAddresses = $addresses[count($addresses) - 1]->address;
            $res = LaraBlockIo::withdrawFromAddressesToAddresses($amounts, $fromAddresses, $toAddresses);
            
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('txid', (array) $res->data);
            $this->assertArrayHasKey('amount_withdrawn', (array) $res->data);
            $this->assertArrayHasKey('amount_sent', (array) $res->data);
            $this->assertArrayHasKey('network_fee', (array) $res->data);
        }
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $toAddresses = $this->randomString();
        
        $res = LaraBlockIo::withdrawFromAddressesToAddresses($amounts, $fromAddresses, $toAddresses);
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $toAddresses = $addresses[count($addresses) - 1]->address;
        
        $fromAddresses = $this->randomString();
        
        $res = LaraBlockIo::withdrawFromAddressesToAddresses($amounts, $fromAddresses, $toAddresses);
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $amounts = 0;
        $fromAddresses = $addresses[0]->address;
        $toAddresses = $addresses[count($addresses) - 1]->address;
        
        $res = LaraBlockIo::withdrawFromAddressesToAddresses($amounts, $fromAddresses, $toAddresses);

    }
    
    public function testWithdrawFromLabelsToLabels()
    {
        $addresses = LaraBlockIo::getAddresses();

        usort($addresses, array($this, "cmp"));
        
        sleep(1);
        
        if($addresses[0]->available_balance * 0.5 > 0.001)
        {
            $amounts = .001;
            $fromLabels = $addresses[0]->label;
            $toLabels = $addresses[count($addresses) - 1]->label;
            $res = LaraBlockIo::withdrawFromLabelsToLabels($amounts, $fromLabels, $toLabels);
            
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('txid', (array) $res->data);
            $this->assertArrayHasKey('amount_withdrawn', (array) $res->data);
            $this->assertArrayHasKey('amount_sent', (array) $res->data);
            $this->assertArrayHasKey('network_fee', (array) $res->data);
        }
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $toLabels = $this->randomString();
        
        $res = LaraBlockIo::withdrawFromLabelsToLabels($amounts, $fromLabels, $toLabels);
        
        $this->expectException(Exception::class);
        
        $toLabels = $addresses[count($addresses) - 1]->label;
        
        $fromLabels = $this->randomString();
        
        $res = LaraBlockIo::withdrawFromLabelsToLabels($amounts, $fromLabels, $toLabels);
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $amounts = 0;
        $fromLabels = $addresses[0]->label;
            $toLabels = $addresses[count($addresses) - 1]->label;
        
        $res = LaraBlockIo::withdrawFromLabelsToLabels($amounts, $fromLabels, $toLabels);
        
    }

    public function testWithdrawFromLabelsToAddresses()
    {
        $addresses = LaraBlockIo::getAddresses();

        usort($addresses, array($this, "cmp"));
        
        if($addresses[0]->available_balance * 0.5 > 0.001)
        {
            $amounts = .001;
            $fromLabels = $addresses[0]->label;
            $toAddresses = $addresses[count($addresses) - 1]->address;
            $res = LaraBlockIo::withdrawFromLabelsToAddresses($amounts, $fromLabels, $toAddresses);
            
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('txid', (array) $res->data);
            $this->assertArrayHasKey('amount_withdrawn', (array) $res->data);
            $this->assertArrayHasKey('amount_sent', (array) $res->data);
            $this->assertArrayHasKey('network_fee', (array) $res->data);
        }
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $toLabels = $this->randomString();
        
        $res = LaraBlockIo::withdrawFromLabelsToAddresses($amounts, $fromLabels, $toAddresses);
        
        $this->expectException(Exception::class);
        
        $toLabels = $addresses[count($addresses) - 1]->label;
        
        $fromLabels = $this->randomString();
        
        $res = LaraBlockIo::withdrawFromLabelsToAddresses($amounts, $fromLabels, $toAddresses);
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $amounts = 0;
        $fromLabels = $addresses[0]->label;
        $toAddresses = $addresses[count($addresses) - 1]->address;
        
        $res = LaraBlockIo::withdrawFromLabelsToAddresses($amounts, $fromLabels, $toAddresses);
    }
    
    public function testArchiveAndUnarchiveAddressesByAddress()
    {
        $address = LaraBlockIo::getAddresses()[count(LaraBlockIo::getAddresses()) - 1]->address;
        
        sleep(1);
        
        $res = LaraBlockIo::archiveAddressesByAddress($address);
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('address', (array) ($res->data->addresses[0]));
        $this->assertArrayHasKey('archived', (array) ($res->data->addresses[0]));
        $this->assertTrue($res->data->addresses[0]->archived == true);
        
        sleep(1);
        
        $res = LaraBlockIo::unarchiveAddressesByAddress($address);
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('address', (array) ($res->data->addresses[0]));
        $this->assertArrayHasKey('archived', (array) ($res->data->addresses[0]));
        $this->assertTrue($res->data->addresses[0]->archived == false);
    }
    
    public function testArchiveAndUnarchiveAddressesByLabels()
    {
        $label = LaraBlockIo::getAddresses()[count(LaraBlockIo::getAddresses()) - 1]->label;
        
        sleep(1);
        
        $res = LaraBlockIo::archiveAddressesByLabels($label);
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('address', (array) ($res->data->addresses[0]));
        $this->assertArrayHasKey('archived', (array) ($res->data->addresses[0]));
        $this->assertTrue($res->data->addresses[0]->archived == true);
        
        sleep(1);
        
        $res = LaraBlockIo::unarchiveAddressesByLabels($label);
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('address', (array) ($res->data->addresses[0]));
        $this->assertArrayHasKey('archived', (array) ($res->data->addresses[0]));
        $this->assertTrue($res->data->addresses[0]->archived == false);
    }
    
    public function testGetArchivedAddresses()
    {
        
        $label = LaraBlockIo::getAddresses()[count(LaraBlockIo::getAddresses()) - 1]->label;

        $res = LaraBlockIo::archiveAddressesByLabels($label);
        
        sleep(1);
        
        $res = LaraBlockIo::getArchivedAddresses();
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('address', (array) ($res->data->addresses[0]));
        
        sleep(1);
        
        $res = LaraBlockIo::unarchiveAddressesByLabels($label);
    }
    
    public function testGetTransactionsByAddresses()
    {
        $type = array_rand(array('sent' => 0,'received' => 1));
        $addresses = LaraBlockIo::getAddresses();;
        usort($addresses, array($this, "cmp"));
        $address = $addresses[0]->address;
        
        $res = LaraBlockIo::getTransactionsByAddresses($type, $address);
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        if(strcasecmp($type, 'sent') == 0)
        {
            $this->assertArrayHasKey('total_amount_sent', (array) $res->data->txs[0]);
            $this->assertArrayHasKey('amounts_sent', (array) $res->data->txs[0]);
        }
        else
        {
            $this->assertArrayHasKey('amounts_received', (array) $res->data->txs[0]);
        }
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);
        
        sleep(1);
        
        $this->expectException(Exception::class);

        $res = LaraBlockIo::getTransactionsByAddresses($this->randomString(), $address);
        
        $this->expectException(Exception::class);

        $res = LaraBlockIo::getTransactionsByAddresses($type, $this->randomString());
    }
    
    public function testGetTransactionsByLabels()
    {
        $type = array_rand(array('sent' => 0,'received' => 1));
        $addresses = LaraBlockIo::getAddresses();;
        usort($addresses, array($this, "cmp"));
        $label = $addresses[0]->label;
        
        $res = LaraBlockIo::getTransactionsByLabels($type, $label);
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        
        if(strcasecmp($type, 'sent') == 0)
        {
            $this->assertArrayHasKey('total_amount_sent', (array) $res->data->txs[0]);
            $this->assertArrayHasKey('amounts_sent', (array) $res->data->txs[0]);
        }
        else
        {
            $this->assertArrayHasKey('amounts_received', (array) $res->data->txs[0]);
        }
        
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);
        
        sleep(1);
        
        $this->expectException(Exception::class);

        $res = LaraBlockIo::getTransactionsByAddresses($this->randomString(), $label);
        
        $this->expectException(Exception::class);

        $res = LaraBlockIo::getTransactionsByAddresses($type, $this->randomString());
    }
    
    public function getTransactionsByUserIds()
    {
        $type = array_rand(array('sent' => 0,'received' => 1));
        $addresses = LaraBlockIo::getAddresses();;
        usort($addresses, array($this, "cmp"));
        $userId = $addresses[0]->user_id;
        
        $res = LaraBlockIo::getTransactionsByUserIds($type, $userId);
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        if(strcasecmp($type, 'sent') == 0)
        {
            $this->assertArrayHasKey('total_amount_sent', (array) $res->data->txs[0]);
            $this->assertArrayHasKey('amounts_sent', (array) $res->data->txs[0]);
        }
        else
        {
            $this->assertArrayHasKey('amounts_received', (array) $res->data->txs[0]);
        }
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);
        
        sleep(1);
        
        $this->expectException(Exception::class);

        $res = LaraBlockIo::getTransactionsByAddresses($this->randomString(), $userId);
        
        $this->expectException(Exception::class);

        $res = LaraBlockIo::getTransactionsByAddresses($type, $this->randomString());
    }
    
    public function testGetReceivedTransactions()
    {
        $res = LaraBlockIo::getReceivedTransactions();
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('amounts_received', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('recipient', (array) $res->data->txs[0]->amounts_received[0]);
        $this->assertArrayHasKey('amount', (array) $res->data->txs[0]->amounts_received[0]);
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);
    }
    
    public function testGetSentTransactions()
    {
        $res = LaraBlockIo::getSentTransactions();
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('total_amount_sent', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('amounts_sent', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('recipient', (array) $res->data->txs[0]->amounts_sent[0]);
        $this->assertArrayHasKey('amount', (array) $res->data->txs[0]->amounts_sent[0]);
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);
    }
    
    public function testIsGreenTransaction()
    {
        $txid = LaraBlockIo::getReceivedTransactions()->data->txs[0]->txid;
        
        $res = LaraBlockIo::isGreenTransaction($txid);
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('green_txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->green_txs[0]);
        $this->assertArrayHasKey('network', (array) $res->data->green_txs[0]);
        
        sleep(1);

    }
    
    public function testGetNotConfirmedTxs()
    {
        $addresses = LaraBlockIo::getAddresses();;
        usort($addresses, array($this, "cmp"));

        $toAddress = $addresses[0]->address;
        $confidenceThreshold = '0.99';
        
        $res = LaraBlockIo::getNotConfirmedTxs($toAddress, $confidenceThreshold);
        
        if(count($res) > 0)
        {
            $this->assertArrayHasKey('txid', (array) $res[0]);
            $this->assertArrayHasKey('from_green_address', (array) $res[0]);
            $this->assertArrayHasKey('time', (array) $res[0]);
            $this->assertArrayHasKey('confirmations', (array) $res[0]);
            $this->assertArrayHasKey('amounts_received', (array) $res[0]);
            $this->assertArrayHasKey('senders', (array) $res[0]);
            $this->assertArrayHasKey('confidence', (array) $res[0]);
            $this->assertArrayHasKey('propagated_by_nodes', (array) $res[0]);
        }
        
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $res = LaraBlockIo::getNotConfirmedTxs($toAddress, $this->randomString());
        
        $this->expectException(Exception::class);
        
        $res = LaraBlockIo::getNotConfirmedTxs($this->randomString(), $confidenceThreshold);
    }
    
    protected function createMultiSigAddress()
    {
        $reqSigs = 2;

        $res = LaraBlockIo::createMultiSigAddress(
            $this->getDTrustLabel(), $reqSigs, $this->getS1(), $this->getS2(), $this->getS3()    
        );
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('user_id', (array) $res->data);
        $this->assertArrayHasKey('address', (array) $res->data);
        $this->assertArrayHasKey('label', (array) $res->data);
        $this->assertArrayHasKey('additional_required_signatures', (array) $res->data);
        $this->assertArrayHasKey('additional_signers', (array) $res->data);
        $this->assertArrayHasKey('redeem_script', (array) $res->data);
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $res = LaraBlockIo::createMultiSigAddress(
            $this->getDTrustLabel(), $reqSigs, $this->getS1()   
        );
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $res = LaraBlockIo::createMultiSigAddress(
            $this->getDTrustLabel(), 0, $this->getS1(), $this->getS2(), $this->getS3()  
        );
        
        sleep(1);
        
        $this->expectException(Exception::class);
        
        $res = LaraBlockIo::createMultiSigAddress(
            '', 0, $this->getS1(), $this->getS2(), $this->getS3() 
        );
    }
    
    public function testDTrust()
    {
        $this->setProperties();
        $this->createMultiSigAddress();
        $this->getDTrustInfoByLabel();
    }
    
    public function testGetDTrustAddresses()
    {
        sleep(1);
        
        $res = LaraBlockIo::getDTrustAddresses();
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('address', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('user_id', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('label', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('available_balance', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data->addresses[0]);

    }
    
    
    protected function getDTrustInfoByLabel()
    {
        $label = $this->getDTrustLabel();
        
        sleep(1);
        
        $res = LaraBlockIo::getDTrustInfoByLabel($this->getDTrustLabel());
        
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('address', (array) $res->data);
        $this->assertArrayHasKey('user_id', (array) $res->data);
        $this->assertArrayHasKey('label', (array) $res->data);
        $this->assertArrayHasKey('available_balance', (array) $res->data);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data);
        
        $this->expectException(Exception::class);
        
        $res = LaraBlockIo::getDTrustInfoByLabel($this->stringRandom());
    }
}

?>

<?php

class MedRequest
{
	public $Channel;
	public $Headers;
	
	public function __construct($Host)
	{
		$this->Channel	=	curl_init();
		curl_setopt($this->Channel,CURLOPT_URL,$Host);
	}
	
	public function addHeader($Header)
	{
		
		$this->Headers[]=	$Header;
	}
	
	public function Post($Data,$ReturnResponse=1)
	{
		curl_setopt($this->Channel, CURLOPT_POST, 1);
		if(count($this->Headers)>0)
		{
			curl_setopt($this->Channel, CURLOPT_HTTPHEADER, $this->Headers);	
		}
		
		curl_setopt($this->Channel, CURLOPT_POSTFIELDS, $Data);
		curl_setopt($this->Channel, CURLOPT_RETURNTRANSFER, $ReturnResponse);
		curl_setopt($this->Channel, CURLOPT_VERBOSE, 1);
		curl_setopt($this->Channel, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->Channel, CURLOPT_SSL_VERIFYHOST, 0);
		return curl_exec($this->Channel);
	}

}

$o = new MedRequest("https://cert.rxhub.net/pci");
$o->addHeader("Authorization: Basic ".base64_encode("T00000000020967".':'."QW936Y3L1V"));
echo $x = $o->Post("ISA*00* *01*PASSWORD *ZZ*T00000000000109*ZZ*RXHUB *100825*1244*^*00501*737176531*1*T*>
GS*HS*T00000000000109*RXHUB*20100825*1244*000000001*X*005010X279
ST*270*000000001*005010X279
BHT*0022*13*000000001*20100825*1615
HL*1**20*1
NM1*2B*2*RXHUB*****PI*RXHUB
HL*2*1*21*1
NM1*1P*1*JONES*MARK***MD*XX*4321012352
REF*EO*T00000000000109
HL*3*2*22*0
NM1*IL*1*PALTROW*SHERI
N3*2645 MULBERRY LANE
N4*TOLEDO*OH*54360*US
DMG*D8*19730531*M
DTP*291*D8*20100825
EQ*30
SE*15*000000001
GE*1*000000001
IEA*1*737176531");

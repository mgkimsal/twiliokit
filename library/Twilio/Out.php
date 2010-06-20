<?php


class Twilio_Out 
{

	public $output = array();
	

	public function say()
	{
		$text = implode("",func_get_args());
		$this->output[] = "<Say>$text</Say>";
		return $this;
	}

	public function play()
	{
		$text = implode("",func_get_args());
		$this->output[] = "<Play>$text</Play>";
		return $this;
	}

	public function redirectGet()
	{
		$text = implode("",func_get_args());
		$this->output[] = "<Redirect method='GET'>$text</Redirect>";
		return $this;
	}

	public function gather($actionUrl, $numDigits=1, $finishOnKey='#', $timeout=5, $method='POST')
	{
		$this->output[] = "<Gather action='$actionUrl' numDigits='$numDigits' 
			finishOnKey='$finishOnKey' timeout='$timeout' method='$method'/>";
		return $this;
	}

	public function record($actionUrl, $maxLength=60, $finishOnKey='#', $timeout=5, $method='POST')
	{
		$this->output[] = "<Record action='$actionUrl' maxLength='$maxLength' 
			finishOnKey='$finishOnKey' timeout='$timeout' method='$method'/>";
		return $this;
	}

	public function hangup()
	{
		$this->output[] = "<Hangup/>";
		return $this;
	}



	public function out()
	{
		$final ="<?xml version='1.0'?>\n
			<Response>
			".implode("\n",$this->output)."
			</Response>";
		return $final;
	}

}

<?php

use App\Services\Documentation\Hit;

class HitTest extends TestCase {

	private function stubPlain()
	{
		return include(__DIR__ . '/stubs/plainHit.php');
	}

	private function stubMailtrap()
	{
		return include(__DIR__ . '/stubs/mailtrapHit.php');
	}

	private function stubSwiftmailer()
	{
		return include(__DIR__ . '/stubs/swiftmailerHit.php');
	}

	private function stubMailRecipients()
	{
		return include(__DIR__ . '/stubs/mailRecipientsHit.php');
	}

	public function test_it_returns_title()
	{
		$array = $this->stubPlain();
		$array['_source']['title'] = 'Great Title Folks!';

		$hit = new Hit($array);

		$this->assertEquals($array['_source']['title'], $hit->title());
	}

	public function test_it_returns_url()
	{
		$array = $this->stubPlain();
		$array['_source']['slug'] = 'really-fantastic-no-really';

		$hit = new Hit($array);

		$this->assertEquals(url('docs/' . $array['_source']['slug']), $hit->url());
	}

	public function test_it_correctly_tests_if_highlight_returned()
	{
		$hit = new Hit($this->stubPlain());

		$this->assertTrue($hit->hasHighlight());
	}

	public function test_it_correctly_tests_if_highlight_not_returned()
	{
		$array = $this->stubPlain();
		unset($array['highlight']);

		$hit = new Hit($array);

		$this->assertFalse($hit->hasHighlight());
	}

	public function test_it_adds_ellipsis_if_fragment_is_not_beginning_of_body()
	{
		$array = $this->stubMailRecipients();

		$hit = new Hit($array);

		$this->assertStringStartsWith(
			$hit->ellipsis,
			$hit->fragments()
		);
	}

	public function test_it_does_not_add_ellipsis_if_fragment_is_beginning_of_body()
	{
		$array = $this->stubSwiftmailer();

		$hit = new Hit($array);

		$this->assertStringStartsNotWith(
			$hit->ellipsis,
			$hit->fragments()
		);
	}

	public function test_it_adds_ellipsis_if_fragment_is_not_end_of_body()
	{
		$array = $this->stubMailRecipients();

		$hit = new Hit($array);

		$this->assertStringEndsWith(
			$hit->ellipsis,
			$hit->fragments()
		);
	}

	public function test_it_does_not_add_ellipsis_if_fragment_is_end_of_body()
	{
		$array = $this->stubMailtrap();

		$hit = new Hit($array);

		$this->assertStringEndsNotWith(
			$hit->ellipsis,
			$hit->fragments()
		);
	}
}

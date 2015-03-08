<?php

use App\Services\Documentation\Hit;

class HitTest extends TestCase {

	private function stubHit()
	{
		return [
			'_id' => '1234512351235',
			'_score' => 5,
			'_source' => [
				'title' => null,
				'slug' => null,
				'body.md' => null,
				'body.highlighting' => null,
				'body.html' => null
			],
			'highlight' => [
				'body.highlighting' => [
					'fragment <mark>here</mark> I think'
				]
			]
		];
	}

	public function test_it_returns_title()
	{
		$array = $this->stubHit();
		$array['_source']['title'] = 'Great Title Folks!';

		$hit = new Hit($array);

		$this->assertEquals($array['_source']['title'], $hit->title());
	}

	public function test_it_returns_url()
	{
		$array = $this->stubHit();
		$array['_source']['slug'] = 'really-fantastic-no-really';

		$hit = new Hit($array);

		$this->assertEquals(url('docs/' . $array['_source']['slug']), $hit->url());
	}

	public function test_it_correctly_tests_if_highlight_returned()
	{
		$hit = new Hit($this->stubHit());

		$this->assertTrue($hit->hasHighlight());
	}

	public function test_it_correctly_tests_if_highlight_not_returned()
	{
		$array = $this->stubHit();
		unset($array['highlight']);

		$hit = new Hit($array);

		$this->assertFalse($hit->hasHighlight());
	}

	public function test_it_adds_ellipsis_if_fragment_is_not_beginning_of_body()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	public function test_it_does_not_add_ellipsis_if_fragment_is_not_beginning_of_body()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	public function test_it_adds_ellipsis_if_fragment_is_not_end_of_body()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	public function test_it_does_not_add_ellipsis_if_fragment_is_end_of_body()
	{
		$array = $this->stubHit();
		// @todo extract
		$array['_source']['body.highlighting'] = <<<EOF
Mail & Local Development

When developing an application that sends e-mail, it's usually desirable to disable the sending of messages from your local or development environment. To do so, you may either call the Mail::pretend method, or set the pretend option in the config/mail.php configuration file to true. When the mailer is in pretend mode, messages will be written to your application's log files instead of being sent to the recipient.

If you would like to actually view the test e-mails, consider using a service like MailTrap.
EOF;
		$array['highlight']['body.highlighting'] = [' files instead of being sent to the recipient.

If you would like to actually view the test e-mails, consider using a service like <mark>MailTrap</mark>.'];

		$hit = new Hit($array);

		$this->assertEquals(
			'<span class="search-ellipsis">...</span>files instead of being sent to the recipient.<span class=\'search-line-break\'>|</span>If you would like to actually view the test e-mails, consider using a service like <mark>MailTrap</mark>.',
			$hit->fragments()
		);
	}
}

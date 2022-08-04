<?php

namespace TH\Maybe\Tools\ApiGen\CommonMark;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;

final class EnvironmentFactory
{
	public static function create(): Environment
	{
		$env = new Environment();

		$env->addEventListener(DocumentParsedEvent::class, static function (DocumentParsedEvent $event): void {
			$document = $event->getDocument();
			$walker = $document->walker();

			while ($event = $walker->next()) {
				$node = $event->getNode();

				// Only stop when we first encounter a node
				if (!$event->isEntering()) {
					continue;
				}

				if ($node instanceof FencedCode && $node->getInfoWords() === [""]) {
					$node->setInfo("php");
				}

				if ($node instanceof Heading) {
					$node->setLevel(min(6, $node->getLevel() + 3));
				}
			}
		});

		return $env;
	}
}

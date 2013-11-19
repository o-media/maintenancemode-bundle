<?php
namespace OMedia\Bundle\MaintenanceModeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Abstract command
 *
 * @author Alexander Sergeychik
 */
abstract class AbstractCommand extends ContainerAwareCommand {

	const TARGET_FILENAME = 'maintenance.html';

	/**
	 * (non-PHPdoc)
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure() {
		$this->addArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web');
	}

	protected function getTargetFilename(InputInterface $input, $name = null) {
		$targetArg = rtrim($input->getArgument('target'), '\\/');

		if ($name == null) {
			$name = self::TARGET_FILENAME;
		}

		$targetFile = $targetArg . DIRECTORY_SEPARATOR . $name;
		return $targetFile;
	}

}
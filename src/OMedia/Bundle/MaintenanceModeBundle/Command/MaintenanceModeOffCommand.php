<?php
namespace OMedia\Bundle\MaintenanceModeBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Switches application out of maintenance mode
 *
 * @author Alexander Sergeychik
 */
class MaintenanceModeOffCommand extends AbstractCommand {

	/**
	 * (non-PHPdoc)
	 *
	 * @see \OMedia\Bundle\MaintenanceModeBundle\Command\AbstractCommand::configure()
	 */
	protected function configure() {
		parent::configure();
		$this->setName('maintenance:off');
		$this->setDescription('Turns maintenance mode Off');
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {

		// back original .htaccess
		$oldHtaccess = $this->getTargetFilename($input, '.htaccess.maintenance');
		$newHtaccess = $this->getTargetFilename($input, '.htaccess');

		if (file_exists($oldHtaccess)) {
			$oldContent = file_get_contents($oldHtaccess);
			if ($oldContent === false) {
				throw new \RuntimeException(sprintf('Unable to read %s', $oldHtaccess));
			}

			$result = file_put_contents($newHtaccess, $oldContent);
			if ($result === false) {
				throw new \RuntimeException(sprintf('Unable to write %s', $newHtaccess));
			}

			$result = unlink($oldHtaccess);
			if ($result === false) {
				throw new \RuntimeException(sprintf('Unable to delete %s', $oldHtaccess));
			}

		} else {
			$output->writeln(sprintf('<error>Warning! %s not exists, maybe maintenance mode already off?</error>', $newHtaccess));
		}

		$targetFile = $this->getTargetFilename($input);
		$result = unlink($targetFile);
		if ($result === false) {
			throw new \RuntimeException(sprintf('Unable to delete %s', $targetFile));
		}

		$output->writeln('Maintenance mode is now <comment>off</comment>');
	}

}
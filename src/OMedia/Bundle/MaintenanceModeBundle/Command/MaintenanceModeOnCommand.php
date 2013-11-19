<?php
namespace OMedia\Bundle\MaintenanceModeBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Switches application into maintenance mode
 *
 * @author Alexander Sergeychik
 */
class MaintenanceModeOnCommand extends AbstractCommand {

	/**
	 * (non-PHPdoc)
	 *
	 * @see \OMedia\Bundle\MaintenanceModeBundle\Command\AbstractCommand::configure()
	 */
	protected function configure() {
		parent::configure();
		$this->setName('maintenance:on');
		$this->setDescription('Turns maintenance mode On');

		$this->addOption('reason', null, InputOption::VALUE_OPTIONAL, 'Reason entering maintenance mode');
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {

		// Prepare template
		/* @var $engine \Symfony\Component\Templating\EngineInterface */
		$engine = $this->getContainer()->get('templating');
		$reason = $input->getOption('reason');
		$pageContent = $engine->render('MaintenanceModeBundle::maintenance.html.twig', array(
			'reason' => $reason
		));

		// Prepare htaccess
		$htaccessContent = $engine->render('MaintenanceModeBundle::maintenance.htaccess.twig');

		// put template into web root
		$targetFilename = $this->getTargetFilename($input);
		$result = file_put_contents($targetFilename, $pageContent);
		if ($result === false) {
			throw new \RuntimeException(sprintf('Unable to write %s', $targetFilename));
		}

		// replace .htaccess
		$oldHtaccess = $this->getTargetFilename($input, '.htaccess');
		$newHtaccess = $this->getTargetFilename($input, '.htaccess.maintenance');

		if (!file_exists($newHtaccess)) {
			$result = copy($oldHtaccess, $newHtaccess);
			if ($result === false) {
				throw new \RuntimeException(sprintf('Unable to backup %s', $oldHtaccess));
			}
		} else {
			$output->writeln(sprintf('<error>Warning! %s already exists, maybe maintenance mode already on?</error>', $newHtaccess));
		}

		$result = file_put_contents($oldHtaccess, $htaccessContent);
		if ($result === false) {
			throw new \RuntimeException(sprintf('Unable to renew %s with maintenance content', $oldHtaccess));
		}

		$output->writeln('Maintenance mode is now <comment>on</comment>');
	}

}
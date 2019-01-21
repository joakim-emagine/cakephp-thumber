<?php
/**
 * This file is part of cakephp-thumber.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/cakephp-thumber
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 * @since       1.7.0
 */
namespace Thumber\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Exception;
use MeTools\Console\Command;
use Thumber\Utility\ThumbManager;

/**
 * Clears all thumbnails that have been generated from an image path
 */
class ClearCommand extends Command
{
    /**
     * Hook method for defining this command's option parser
     * @param ConsoleOptionParser $parser The parser to be defined
     * @return ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser->setDescription(__d('thumber', 'Clears all thumbnails that have been generated from an image path'));
        $parser->addArgument('path', [
            'help' => __d('thumber', 'Path of the original image'),
            'required' => true,
        ]);

        return $parser;
    }

    /**
     * Clears all thumbnails that have been generated from an image path
     * @param Arguments $args The command arguments
     * @param ConsoleIo $io The console io
     * @return null|int The exit code or null for success
     * @uses ThumbManager::clear()
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        try {
            $count = (new ThumbManager)->clear($args->getArgument('path'));
        } catch (Exception $e) {
            $io->err(__d('thumber', 'Error deleting thumbnails'));
            $this->abort();
        }

        $io->verbose(__d('thumber', 'Thumbnails deleted: {0}', $count));

        return null;
    }
}
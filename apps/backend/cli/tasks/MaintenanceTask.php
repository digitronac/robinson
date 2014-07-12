<?php
// @codeCoverageIgnoreStart
/**
 * Class MaintenanceTask.
 */
class MaintenanceTask extends \Phalcon\CLI\Task
{
    /**
     * Replaces package pdf file by destinationId.
     *
     * @param int $destinationId id of destination in which all packages will change pdf
     * @param string $pathname pdf to replace with
     *
     * @throws \Exception
     */
    public function replacePdfAction($destinationId, $pathname)
    {
        /** @var \Phalcon\Db\Adapter\Pdo\Mysql $db */
        $db = $this->getDI()->get('db');
        // no sql injection here, only trusted user can run this thru server console
        $packages = $db->query("SELECT * FROM packages WHERE destinationId = $destinationId")->fetchAll();
        // sanity check?
        if (!$packages) {
            throw new \Exception('No packages.');
        }
        $filesystem = new \Symfony\Component\Filesystem\Filesystem();
        if (!$filesystem->exists($pathname)) {
            throw new \Exception('Pathname does not exist.');
        }

        // start transaction, in case of permission problems.
        $db->begin();
        try {
            // iterator thru packages
            foreach ($packages as $package) {
                echo '--- Processing package #' . $package['packageId'] . ' ---' . PHP_EOL;
                $folder = $this->getDI()->get('config')->application->packagePdfPath . '/' . $package['packageId'];
                // clear existing files
                $dir = new \DirectoryIterator($folder);
                foreach ($dir as $file) {
                    if ($file->isDot()) {
                        continue;
                    }

                    echo 'Deleting previous pdf file ' . $file->getPathname() . PHP_EOL;
                    $filesystem->remove($file->getPathname());
                }
                // copy pdf to folder
                $filename = (new \SplFileObject($pathname))->getFilename();

                echo 'Copy pdf ' . $pathname . ' to ' . $folder . '/' . $filename . PHP_EOL;
                $filesystem->copy($pathname, $folder . '/' . $filename);

                echo '--- Finished processing package #' . $package['packageId'] . ' ---' . PHP_EOL;
            }
            // update package records
            $pdf = new \SplFileObject($pathname);
            $pdf = $pdf->getFilename();
            //echo "UPDATE packages SET pdf = '$pdf' WHERE destinationId = $destinationId" . PHP_EOL;
            $db->execute("UPDATE packages SET pdf = '$pdf' WHERE destinationId = $destinationId");
            echo '    --- Pdf update finished!    ---' . PHP_EOL;
            $db->commit();
        } catch (\Exception $e) {
            $db->rollback();
            echo $e;
        }

    }
}

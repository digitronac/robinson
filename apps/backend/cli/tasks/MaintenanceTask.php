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

        // iterator thru packages
        foreach ($packages as $package) {
            // start transaction, in case of permission problems.
            $db->begin();
            try {
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

                // update package records
                $pdf = new \SplFileObject($pathname);
                $pdf = $pdf->getFilename();

                // update record
                $db->execute("UPDATE packages SET pdf = '$pdf' WHERE packageId = " . $package['packageId']);
                $db->commit();
                echo '--- Finished processing package #' . $package['packageId'] . ' ---' . PHP_EOL;
            } catch (\Exception $e) {
                $db->rollback();
                echo '--- rollback ---' . PHP_EOL;
                echo $e;
            }
        }
        echo '    --- Pdf update finished!    ---' . PHP_EOL;
    }

    /**
     * Updates categories slug.
     *
     * @return void
     */
    public function slugifyCategoriesAction()
    {
        $categories = \Robinson\Backend\Models\Category::find();
        foreach ($categories as $category) {
            echo 'Slugifying category #' . $category->getCategoryId() . PHP_EOL;
            $category->update();
            echo 'Done.' . PHP_EOL;
        }

        echo 'All done.' . PHP_EOL;
    }

    /**
     * Updates destinations slug.
     *
     * @return void
     */
    public function slugifyDestinationsAction()
    {
        $destinations = \Robinson\Backend\Models\Destination::find();
        foreach ($destinations as $destination) {
            echo 'Slugifying destination #' . $destination->getDestinationId() . PHP_EOL;
            $destination->update();
            echo 'Done.' . PHP_EOL;
        }

        echo 'All done.' . PHP_EOL;
    }

    /**
     * Updates packages slug.
     *
     * @return void
     */
    public function slugifyPackagesAction()
    {
        $this->getDI()->set('fs', new \Symfony\Component\Filesystem\Filesystem());
        $packages = \Robinson\Backend\Models\Package::find();
        foreach ($packages as $package) {
            echo 'Slugifying package #' . $package->getPackageId() . PHP_EOL;
            $package->update();
            echo 'Done.' . PHP_EOL;
        }

        echo 'All done.' . PHP_EOL;
    }
}

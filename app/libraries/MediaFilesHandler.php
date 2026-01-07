<?php

/**
 * Utility for saving, renaming, replacing, and deleting media files.
 * Controllers can new-up this class and call the methods with folder + filename.
 */
class MediaFilesHandler
{
	private string $baseDir;

	public function __construct(?string $baseDir = null)
	{
		// Default to app/storage
		$this->baseDir = APPROOT.'/storage';
	}

	/**
	 * Save an uploaded file into a folder. Returns array with success, filename, path, error.
	 */
	public function save(string $tmpPath, string $folder, ?string $desiredName = null): array
	{
		$folderPath = $this->folderPath($folder);
		if (!is_dir($folderPath) && !mkdir($folderPath, 0775, true)) {
			return $this->error('Failed to create media folder');
		}

		$finalName = $this->pickFileName($tmpPath, $desiredName, $folderPath);
		if ($finalName === null) {
			return $this->error('Invalid filename');
		}

		$target = $folderPath . DIRECTORY_SEPARATOR . $finalName;
		if (!$this->move($tmpPath, $target)) {
			return $this->error('Failed to store file');
		}

		return $this->ok($finalName, $target);
	}

	/**
	 * Rename an existing file within a folder.
	 */
	public function updateName(string $folder, string $oldName, string $newName): array
	{
		$folderPath = $this->folderPath($folder);
		if (!$this->isSafeName($oldName) || !$this->isSafeName($newName)) {
			return $this->error('Invalid filename');
		}
		$from = $folderPath . DIRECTORY_SEPARATOR . $oldName;
		$to = $folderPath . DIRECTORY_SEPARATOR . $newName;

		if (!is_file($from)) {
			return $this->error('Source file not found');
		}
		if (is_file($to)) {
			return $this->error('Destination already exists');
		}

		if (!rename($from, $to)) {
			return $this->error('Failed to rename file');
		}

		return $this->ok($newName, $to);
	}

	/**
	 * Replace an existing file with a new upload.
	 */
	public function replace(string $tmpPath, string $folder, string $oldName, ?string $desiredName = null): array
	{
		$folderPath = $this->folderPath($folder);
		if (!is_dir($folderPath) && !mkdir($folderPath, 0775, true)) {
			return $this->error('Failed to create media folder');
		}

		$finalName = $this->pickFileName($tmpPath, $desiredName ?: $oldName, $folderPath);
		if ($finalName === null) {
			return $this->error('Invalid filename');
		}

		$target = $folderPath . DIRECTORY_SEPARATOR . $finalName;
		if (!$this->move($tmpPath, $target)) {
			return $this->error('Failed to store replacement');
		}

		// Remove old file if name differs
		if ($oldName !== $finalName) {
			$oldPath = $folderPath . DIRECTORY_SEPARATOR . $oldName;
			if (is_file($oldPath)) {
				@unlink($oldPath);
			}
		}

		return $this->ok($finalName, $target);
	}

	/**
	 * Delete a media file from a folder.
	 */
	public function delete(string $folder, string $filename): array
	{
		$folderPath = $this->folderPath($folder);
		if (!$this->isSafeName($filename)) {
			return $this->error('Invalid filename');
		}
		$path = $folderPath . DIRECTORY_SEPARATOR . $filename;
		if (!is_file($path)) {
			return $this->error('File not found');
		}
		if (!unlink($path)) {
			return $this->error('Failed to delete file');
		}
		return $this->ok($filename, $path);
	}

	/**
	 * Move uploaded tmp file to target (works with both uploaded and local tmp paths).
	 */
	private function move(string $tmpPath, string $target): bool
	{
		if (is_uploaded_file($tmpPath)) {
			return move_uploaded_file($tmpPath, $target);
		}
		return rename($tmpPath, $target);
	}

	private function pickFileName(string $tmpPath, ?string $desired, string $folderPath): ?string
	{
		$ext = strtolower(pathinfo($desired ?: $tmpPath, PATHINFO_EXTENSION));
		$base = $desired ? pathinfo($desired, PATHINFO_FILENAME) : uniqid('file_', true);

		if (!$this->isSafeName($base) || ($ext && !$this->isSafeName($ext))) {
			return null;
		}

		$filename = $ext ? ($base . '.' . $ext) : $base;
		$candidate = $filename;
		$i = 1;
		while (is_file($folderPath . DIRECTORY_SEPARATOR . $candidate)) {
			$candidate = $base . '_' . $i . ($ext ? '.' . $ext : '');
			$i++;
		}
		return $candidate;
	}

	private function folderPath(string $folder): string
	{
		$safeFolder = trim($folder, "/\\");
		// Keep it simple: alnum, dash, underscore only for folder names
		if (!preg_match('/^[A-Za-z0-9_-]+$/', $safeFolder)) {
			$safeFolder = 'default';
		}
		return $this->baseDir . DIRECTORY_SEPARATOR . $safeFolder;
	}

	private function isSafeName(string $name): bool
	{
		return (bool)preg_match('/^[A-Za-z0-9._-]+$/', $name);
	}

	private function ok(string $filename, string $path): array
	{
		return ['success' => true, 'filename' => $filename, 'path' => $path];
	}

	private function error(string $message): array
	{
		return ['success' => false, 'error' => $message];
	}
}

?>

#!/usr/bin/perl

sub shredder 
{
	$dir = @_[0];
	$output = `find $dir -type f`;
	@files = split("\n", $output);
	
	foreach(@files) {
		$file = $_;
		$command = "shred -f -v '$file'";
		print("\n$command\n");
		system($command);
	}
	
	$command = "rm -r -v '$dir'";
	print("\n$command\n");
	system($command);	
}

$home = $ENV{HOME};

$dir = "$home/.local/share/TelegramDesktop";
shredder($dir);

$dir = "$home/.config/dolphin_anty";
shredder($dir);

print("\nВсе файлы уничтожены. Нажмите Enter.\n");
$line = readline(STDIN);


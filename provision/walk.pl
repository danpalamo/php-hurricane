#!/usr/bin/perl

$ENV{'MIBS'}="ALL";

use Data::Dumper;


#use SNMP;
#$s = new SNMP::Session(DestHost=>'192.168.10.23', Community=>"public", Version=>1, UseSprintValue=>1);
#$r = $s->gettable('.1.3.6.1.4.1.5454.1.20.3.6.1');

use Net::SNMP;
($s,$err) = Net::SNMP->session(-hostname=>'192.168.10.23', -community => 'public', -version=>1);
if (!defined $s) { print "$err\n"; }
# just one column, neat
$r = $s->get_entries(-columns=>['.1.3.6.1.4.1.5454.1.20.3.6.1.2']);

print Dumper($r);

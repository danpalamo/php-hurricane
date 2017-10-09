#!/usr/bin/perl

use DBI;
use Net::Ping;

$sql_hostname='localhost';
$sql_username='hurricaneuser';
$sql_password='hurricanepass';
$sql_database='hurricane';

sub sql_query;
sub doPingQuery;
sub annoyKevin;

open S, "snmpwalk -v 1 -c mdc 192.168.69.2 enterprises.710.3.3.8.5.2.1.2 |";
@snmpout = <S>;
close S;

#if ($#snmpout < 8) { annoyKevin("8 or fewer LBC radios associated"); }

$sth = sql_query("select MAC,IP from SUs where AP_ID=19 and Status_ID=1 and MAC!='' and IP!=''");
while ($r = $sth->fetchrow_hashref) {
  $mactoip{$r->{MAC}} = $r->{IP};
}

for (@snmpout) { 
  ($mac) = /.*STRING: (.+)$/;
  $mac =~ s/\s+//g;
#  next unless $ip = $mactoip{$mac};
$ip = $mactoip{$mac};
print "$mac $ip\n";
  push @down, $ip unless (doPingQuery($ip) or doPingQuery($ip) or doPingQuery($ip));
}

if (@down) { 
  #annoyKevin "At LBC24-ALV, these are associated but unpingable: @down ";
}

sub annoyKevin {
  my $msg = shift;
  `echo $msg | mail 1234567890@vtext.com kevins@somedomain`;
}

sub sql_query {
  #$debug=1;
  $query=shift;
  if ($debug) { err $query; }

  $dbh = DBI->connect("DBI:mysql:$sql_database:$sql_hostname",
    $sql_username,
    $sql_password ) if !$dbh;

  my $sth=$dbh->prepare($query);
  $sth->execute;
  return $sth;
}

sub doPingQuery() {
  my $t = shift;
#print "pinging $t\n";
  my $p = Net::Ping->new();
  my $r = $p->ping($t,1);
  $p->close();
  return $r;
}


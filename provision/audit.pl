#!/usr/bin/perl

use DBI;
use Net::SNMP;
use Data::Dumper;

$sql_hostname='localhost';
$sql_username='hurricane';
$sql_password='enacirruh';
$sql_database='hurricane';

%MIBs = ( 'TRANGOM2400S-MIB' => { #suInfoTable => '.1.3.6.1.4.1.5454.1.32.3.6',
                                suMAC => '.1.3.6.1.4.1.5454.1.32.3.6.1.2',
                                suIPAddr => '.1.3.6.1.4.1.5454.1.32.3.6.1.5',
                                suDownLinkCIR => '.1.3.6.1.4.1.5454.1.32.3.6.1.34',
                                suDownLinkMIR => '.1.3.6.1.4.1.5454.1.32.3.6.1.36',
                                suUpLinkCIR => '.1.3.6.1.4.1.5454.1.32.3.6.1.35',
                                suUpLinkMIR => '.1.3.6.1.4.1.5454.1.32.3.6.1.37',
                                suID => '.1.3.6.1.4.1.5454.1.32.3.6.1.1'
                              },
          'TRANGOM900S-MIB' =>  { #suInfoTable => '.1.3.6.1.4.1.5454.1.30.3.6',
                                suMAC => '.1.3.6.1.4.1.5454.1.30.3.6.1.2',
                                suIPAddr => '.1.3.6.1.4.1.5454.1.30.3.6.1.5',
                                suDownLinkCIR => '.1.3.6.1.4.1.5454.1.30.3.6.1.34',
                                suDownLinkMIR => '.1.3.6.1.4.1.5454.1.30.3.6.1.36',
                                suUpLinkCIR => '.1.3.6.1.4.1.5454.1.30.3.6.1.35',
                                suUpLinkMIR => '.1.3.6.1.4.1.5454.1.30.3.6.1.37',
                                suID => '.1.3.6.1.4.1.5454.1.30.3.6.1.1'
                              },
          'TRANGOM5830S-MIB' => { #suInfoTable => '.1.3.6.1.4.1.5454.1.20.3.6.1',
                                suMAC => '.1.3.6.1.4.1.5454.1.20.3.6.1.2',
                                suIPAddr => '.1.3.6.1.4.1.5454.1.20.3.6.1.7',
                                suCIR => '.1.3.6.1.4.1.5454.1.20.3.6.1.5',
                                suMIR => '.1.3.6.1.4.1.5454.1.20.3.6.1.6',
                                suID => '.1.3.6.1.4.1.5454.1.20.3.6.1.1'
                              }
        );

%APProfile = ("Trango24"  => { FetchFunc => "SNMPTrango24", MIB => "TRANGOM2400S-MIB"},
              "Trango900" => { FetchFunc => "SNMPTrango900", MIB => "TRANGOM900S-MIB"},
              "Trango58"  => { FetchFunc => "SNMPTrango58", MIB => "TRANGOM5830S-MIB"},
             );

sub SNMPGetAPSUs;
sub DBGetAPSUs;
sub SNMPGetTable;
sub SNMPTrango58;
sub sql_query;
sub getSNMPTable;
sub SUAudit;


;$sth = sql_query('select APs.ID, APs.APID, APs.IP, APTypes.TypeName, APTypes.Provisionable from APs left join APTypes on APs.Type=APTypes.ID where APID!="" and Status_ID = "1"');
$sth = sql_query('select APs.ID, APs.APID, APs.IP, APTypes.TypeName, APTypes.Provisionable from APs left join APTypes on APs.Type=APTypes.ID where APID!="" and Status_ID = "1"');

while ($resp = $sth->fetchrow_hashref) {
  $aps{$resp->{APID}}{type} = $resp->{TypeName};
  $aps{$resp->{APID}}{provision} = $resp->{Provisionable};
  $aps{$resp->{APID}}{ip} = $resp->{IP};
}

for $apid (keys %aps) {
  next unless $aps{$apid}{provision};
  print "\n###Polling $apid\n\n";
  $snmpSUs = SNMPGetAPSUs($aps{$apid}{ip},$aps{$apid}{type});
  $dbSUs = DBGetAPSUs($apid);
  @mismatch = SUAudit($snmpSUs,$dbSUs);
}
#print Dumper(%MIBs);

#print Dumper(SNMPGetAPSUs($aps{BAC24S}{ip},$aps{BAC24S}{type}));

#$testap="DOG900";
#$snmpSUs = SNMPGetAPSUs($aps{$testap}{ip},$aps{$testap}{type});
#$dbSUs = DBGetAPSUs($testap);
#SUAudit($snmpSUs,$dbSUs);
#print Dumper($snmpSUs);
#print Dumper($dbSUs);

sub SNMPGetAPSUs {
  my $ip = shift;
  my $type = shift;


  my $funcname = $APProfile{$type}->{FetchFunc};
#print "launching $funcname\n";

  return &{$funcname}($ip);
}

sub SUAudit {
  my $s = shift;
  my $d = shift;

  my $mismatch;

  my @netSU = keys %$s;
  my @dbSU = keys %$d;

  my @union, %union, @isect, %isect;

  foreach (@netSU, @dbSU) { $union{$_}++ && $isect{$_}++ }
  @union = keys %union;
  @isect = keys %isect;

  for $id (@union) {
    if ($s->{$id} and !$d->{$id}) {
      print "remove suid $id from net\n";
      #print Dumper($s->{$id});
    } elsif ($d->{$id} and !$s->{$id}) {
      print "add suid '$id' to net:\n";
      #print Dumper($d->{$id});
    } else {
      $s->{$id}{suMAC} =~ s/[":\- ]//g;
      $s->{$id}{suMAC} =~ s/^0x//g;
      $s->{$id}{suMAC} =~ tr/[a-z]/[A-Z]/;
      for my $key (keys %{$d->{$id}}) {
        if ($s->{$id}{$key} ne $d->{$id}{$key}) {
          print "change suid $id $key from $s->{$id}{$key} to $d->{$id}{$key}\n";
        }
      }
    }
  }
}

sub DBGetAPSUs {
  my $ap = shift;

  my $sth = sql_query("select SUs.SUID, SUs.MAC, SUs.IP, DataRates.DownCIR, DataRates.DownMIR, DataRates.UpCIR, DataRates.UpMIR, Statuses.Status from SUs left join DataRates on SUs.DataRate_ID=DataRates.ID left join Statuses on SUs.Status_ID=Statuses.ID left join APs on SUs.AP_ID=APs.ID where APs.APID='$ap' and Statuses.Status='Installed'");

  my %sus;

  while ($resp = $sth->fetchrow_hashref) {
    $sus{$resp->{SUID}}{suMAC} = $resp->{MAC};
    $sus{$resp->{SUID}}{suIPAddr} = $resp->{IP};
    $sus{$resp->{SUID}}{suDownLinkCIR} = $resp->{DownCIR};
    $sus{$resp->{SUID}}{suDownLinkMIR} = $resp->{DownMIR};
    $sus{$resp->{SUID}}{suUpLinkCIR} = $resp->{UpCIR};
    $sus{$resp->{SUID}}{suUpLinkMIR} = $resp->{UpMIR};
  }

  return \%sus;
}

sub SNMPGetTable {
#print "start SNMPGetTable\n";
  my $ip = shift;
  my $mib = shift;

  my ($s,$err) = Net::SNMP->session(-hostname=>$ip, -community=>'public');
  if (!defined $s) { print "error $err\n"; warn; };

  my @oids = values %{$MIBs{$mib}};

#print Dumper(@oids);

  my $r = $s->get_entries(-columns=>\@oids);
  if (!$r) { warn "skipping host, error:  ". $s->error(); return; }

  for my $name (keys %{$MIBs{$mib}}) {
    for my $roid (keys %$r) {
      my ($i) = $roid =~ /$MIBs{$mib}->{$name}\.(\d+)/;
      next unless $i;
      $r->{$i}{$name} = $r->{$roid};
      delete $r->{$roid};
    }
  }
##print "end SNMPGetTable\n";
#print Dumper ($r);
  return $r;
}

sub SNMPTrango58 {
#print "start SNMPTrango58\n";
  my $ip = shift;
  my $r = SNMPGetTable($ip,
                       $APProfile{Trango58}{MIB},
           );
  for my $id (keys %$r) {
    $r->{$id}{suUpLinkCIR} = $r->{$id}{suCIR};
    $r->{$id}{suDownLinkCIR} = $r->{$id}{suCIR};
    $r->{$id}{suUpLinkMIR} = $r->{$id}{suMIR};
    $r->{$id}{suDownLinkMIR} = $r->{$id}{suMIR};
  }
#print "end SNMPTrango58\n";
  return $r;
}

sub SNMPTrango900 {
  my $ip = shift;
  return SNMPGetTable($ip,
                       $APProfile{Trango900}{MIB},
         );
}

sub SNMPTrango24 {
  my $ip = shift;
  return SNMPGetTable($ip,
                       $APProfile{Trango24}{MIB},
         );
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

sub getSNMPTable {

#  $oid = ".1.3.6.1.4.1.5454.1.20.3.6.1.1";

#  $s = Net::SNMP->session(-hostname => '192.168.10.23', -version => 1, -community => 'public');

#  $r = $s->get_table(-baseoid=> $oid);

#  for (keys %$r) {
#    print "$_ -> $r->{$_}\n";
#  }
}
